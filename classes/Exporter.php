<?php

use Illuminate\Support;
use LSS\Array2Xml;

// retrieves & formats data from the database for export
class Exporter extends Players
{

    public function __construct()
    {
    }

    function getPlayerStats($search)
    {
        $sql = "
            SELECT roster.name, player_totals.*
            FROM player_totals
                INNER JOIN roster ON (roster.id = player_totals.player_id)
            WHERE ";

        $data = $this->db_query->executeQuery($sql, $search);

        return $this->getStats($data);
    }

    function getPlayers($search)
    {
        $sql = "
            SELECT roster.*
            FROM roster
            WHERE ";

        $data = $this->db_query->executeQuery($sql, $search);
        return collect($data)
            ->map(function ($item, $key) {
                unset($item['id']);
                return $item;
            });
    }

    public function format($data, $format = 'html')
    {
        // return the right data format
        switch ($format) {
            case 'xml':
                header('Content-type: text/xml');
                return $this->formatPlayersXmlData($data)->saveXML();
                break;
            case 'json':
                header('Content-type: application/json');
                return json_encode($data->all());
                break;
            case 'csv':
                header('Content-type: text/csv');
                header('Content-Disposition: attachment; filename="export.csv";');
                return $this->formatPlayersCsvData($data);
                break;
            default: // html
                return $this->formatPlayersHtmlData($data);
                break;
        }
    }
}
