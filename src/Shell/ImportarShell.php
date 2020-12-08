<?php
//Chile: https://www.spensiones.cl/apps/valoresCuotaFondo/vcfAFPxls.php
//Peru: http://www.sbs.gob.pe/app/stats/EstadisticaBoletinEstadistico.asp?p=31

namespace App\Shell;

use Cake\Console\Shell;
use GuzzleHttp\Client as HttpClient;
use Cake\Log\Log;

/**
 * Importar shell command.
 */
class ImportarShell extends Shell
{
    /**
     * @var bool
     */
    protected $processResumenMensual;

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main()
    {
        ini_set('max_execution_time', 0);
        $this->loadModel('Afps');
        $this->loadModel('Cuotas');
        $this->loadModel('Fondos');
        $this->loadModel('Devices');
        $this->out("Starting import");
        $count = $this->parseCuotas(date("Y"), true, false);
        $this->out("Import finished");
        if ($count > 0) {
            $this->loadModel('Preferences');
            $this->loadModel('Rankings');
            $this->Preferences->sendEmailsFromImport();
            $this->Rankings->calculateRanking();
        }
        $this->Devices->sendNotifications();
        $this->out("$count records added");
        return $count;
    }

    /**
     * Procesa las cuotas del año dado
     *
     * @param int $year
     * @param bool $retry Retry from the previous year if no new data is found
     * @param bool $allDates Process all the dates in the response
     * @throws \Exception
     */
    private function parseCuotas($year, $retry = false, $allDates = false) {
        $added = 0;
        $fondos = array('A', 'B', 'C', 'D', 'E');
        $curdate = date("Ymd");
        $data = array();
        $min_date = date("Y-m-d", strtotime("-7 days"));
        if ($allDates) {
            $min_date = "$year-01-01";
        }

        if (!$retry) {
            $min_date = "$year-12-01";
        }

        $client = new HttpClient();
        foreach ($fondos as $fondo) {
          try {
            $res = $client->request('GET', "https://www.spensiones.cl/apps/valoresCuotaFondo/vcfAFPxls.php", [
              "query"=> [
                'aaaaini' => $year,
                'aaaafin' => $year,
                'tf' => $fondo,
                'fecconf' => $curdate
              ],
              'delay' => 5000
            ]);
            if ($res->getStatusCode() != 200) {
                throw new \Exception("Unable to retrieve the information");
            }
            Log::info("Processing data for fund $fondo since date $min_date");
            $data[$fondo] = $this->processResponse($res->getBody(), $min_date);
            foreach ($data[$fondo] as $fecha => $afps) {
                $added += $this->storeData($fondo, $fecha, $afps);
            }
          } catch(\Exception $ex) {
            $this->out($ex->getMessage());
            $added += 0;
          }
        }

        if ($added == 0 && $retry) {
            Log::info("No data, checking previous year");
            $previousYear = (int)$year - 1;
            return $this->parseCuotas($previousYear, false);
        }

        if ($this->processResumenMensual) {
            //$this->generarResumenMensual();
        }

        return $added;
    }

    private function processResponse($response, $minDate) {
        $lines = explode("\n", $response);
        foreach ($lines as $k => $line) {
            $line = trim(str_replace('.', '', $line));
            $lines[$k] = str_replace(',', '.', $line);
            if (strlen(trim($line)) == 0) {
                unset($lines[$k]);
            }
        }

        $lines = array_values($lines);
        if (empty($lines)) {
            return array();
        }
        $linea_afp = explode(';', $lines[1]);
        $afps = array();
        $data = array();
        for ($i = 0; $i < count($linea_afp); $i++) {
            $afp = trim($linea_afp[$i]);
            if ($afp != 'Fecha' && $afp != '') {
                $afps["$i"] = $afp;
            }
        }

        for ($i = 3; $i < count($lines); $i++) {
            $campos = explode(';', $lines[$i]);
            foreach ($afps as $pos => $afp) {
                if (array_key_exists($pos, $campos)) {
                    $fecha = trim($campos[0]);
                    if ($fecha !== 'Fecha' && strlen($fecha) > 0) {
                        if ($fecha >= $minDate) {
                            $data[$fecha][$afp] = array(
                                'valor' => $campos[$pos],
                                'patrimonio' => $campos[(int) $pos + 1]
                            );
                        } else {
                            //Log::info("Fail $fecha >= $minDate");
                        }
                    } else {
                        //Log::info("Error in line $i, expected 'Fecha', got it: '$fecha'");
                    }
                }
            }
        }
        return $data;
    }

    private function storeData($fondoName, $fecha, $afps) {
        $added = 0;
        $this->processResumenMensual = true;
        foreach ($afps as $afpname => $data) {
            if (floatval($data['valor']) <= 0) {
                continue;
            }
            $afp = $this->getAfp($afpname);
            $fondo = $this->getFondo($fondoName);

            $cuota = $this->Cuotas->find()
                ->where([
                    'afp_id' => $afp->id,
                    'fondo_id' => $fondo->id,
                    'fecha' => $fecha
                ])->first();

            if (empty($cuota->id)) {
                $this->Cuotas->query()
                    ->insert(['fecha', 'afp_id', 'fondo_id', 'valor', 'patrimonio'])
                    ->values([
                        'fecha' => $fecha,
                        'afp_id' => $afp->id,
                        'fondo_id' => $fondo->id,
                        'valor' => $data['valor'],
                        'patrimonio' => $data['patrimonio']
                    ])
                    ->execute();
                if ($this->isFinDeMes($fecha)) {
                    $this->processResumenMensual = true;
                }
                $added++;
            } else {
                $this->Cuotas->query()
                    ->update()
                    ->set([
                        'valor' => $data['valor'],
                        'patrimonio' => $data['patrimonio']
                    ])
                    ->where(['id' => $cuota->id])
                    ->execute();
            }
        }
        return $added;
    }

    private function getFondo($fondoName)
    {
        $fondo = $this->Fondos->findByName($fondoName)->first();
        if (empty($fondo)) {
            $this->Fondos->query()
                ->insert(['name', 'api_name', 'country', 'status'])
                ->values([
                    'name' => $fondoName,
                    'api_name' => strtolower($fondoName),
                    'country' => 'CL',
                    'status' => 1
                ])
                ->execute();
            $fondo = $this->Fondos->findByName($fondoName)->first();
        }

        return $fondo;
    }

    private function getAfp($afpName)
    {
        $afp = $this->Afps->findByName($afpName)->first();
        if (empty($afp->id)) {
            $this->Afps->query()
                ->insert(['name', 'api_name', 'country', 'status'])
                ->values([
                    'name' => $afpName,
                    'api_name' => strtolower($afpName),
                    'country' => 'CL',
                    'status' => 1
                ])
                ->execute();
            $afp = $this->Afps->findByName($afpName)->first();
        }

        return $afp;
    }

    private function isFinDeMes($fecha)
    {
        return $fecha === date("Y-m-t", strtotime($fecha));
    }

    private function generarResumenMensual()
    {
        $this->loadModel('Preferences');
        $this->Preferences->sendEmailReporteMensual();
    }
}
