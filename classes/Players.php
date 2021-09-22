<?php

use LSS\Array2XML;

class Players extends DatabaseQueries
{
    public function getStats($data)
    {
        $results = [];
        // calculate totals
        foreach ($data as $row) {
            unset($row['player_id']);
            $row['total_points'] = ($row['3pt'] * 3) + ($row['2pt'] * 2) + $row['free_throws'];
            $row['field_goals_pct'] = $row['field_goals_attempted'] ? (round($row['field_goals'] / $row['field_goals_attempted'], 2) * 100) . '%' : 0;
            $row['3pt_pct'] = $row['3pt_attempted'] ? (round($row['3pt'] / $row['3pt_attempted'], 2) * 100) . '%' : 0;
            $row['2pt_pct'] = $row['2pt_attempted'] ? (round($row['2pt'] / $row['2pt_attempted'], 2) * 100) . '%' : 0;
            $row['free_throws_pct'] = $row['free_throws_attempted'] ? (round($row['free_throws'] / $row['free_throws_attempted'], 2) * 100) . '%' : 0;
            $row['total_rebounds'] = $row['offensive_rebounds'] + $row['defensive_rebounds'];

            array_push($results);
        }

        return collect($results);
    }

    public function formatPlayersXmlData($data)
    {
        // fix any keys starting with numbers
        $keyMap = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        $xmlData = [];
        foreach ($data->all() as $row) {
            $xmlRow = [];
            foreach ($row as $key => $value) {
                $key = preg_replace_callback('(\d)', function ($matches) use ($keyMap) {
                    return $keyMap[$matches[0]] . '_';
                }, $key);
                $xmlRow[$key] = $value;
            }
            $xmlData[] = $xmlRow;
        }

        $xml = Array2XML::createXML('data', [
            'entry' => $xmlData
        ]);

        return $xml;
    }

    public function formatPlayersCsvData($data)
    {
        if (!$data->count()) {
            return;
        }
        $csv = [];
        // extract headings
        // replace underscores with space & ucfirst each word for a decent headings
        $headings = collect($data->get(0))->keys();
        $headings = $headings->map(function ($item, $key) {
            return collect(explode('_', $item))
                ->map(function ($item, $key) {
                    return ucfirst($item);
                })
                ->join(' ');
        });
        $csv[] = $headings->join(',');

        // format data
        foreach ($data as $dataRow) {
            $csv[] = implode(',', array_values($dataRow));
        }

        return implode('\n', $csv);
    }

    public function formatPlayersHtmlData($data)
    {
        if (!$data->count()) {
            return $this->htmlTemplate('Sorry, no matching data was found');
        }

        // extract headings
        // replace underscores with space & ucfirst each word for a decent heading
        $headings = collect($data->get(0))->keys();
        $headings = $headings->map(function ($item, $key) {
            return collect(explode('_', $item))
                ->map(function ($item, $key) {
                    return ucfirst($item);
                })
                ->join(' ');
        });
        $headings = '<tr><th>' . $headings->join('</th><th>') . '</th></tr>';

        // output data
        $rows = [];
        foreach ($data as $dataRow) {
            $row = '<tr>';
            foreach ($dataRow as $key => $value) {
                $row .= '<td>' . $value . '</td>';
            }
            $row .= '</tr>';
            $rows[] = $row;
        }
        $rows = implode('', $rows);
        return $this->htmlTemplate('<table>' . $headings . $rows . '</table>');
    }

    // wrap html in a standard template
    public function htmlTemplate($html)
    {
        return '
            <html>
            <head>
            <style type="text/css">
                body {
                    font: 16px Roboto, Arial, Helvetica, Sans-serif;
                }
                td, th {
                    padding: 4px 8px;
                }
                th {
                    background: #eee;
                    font-weight: 500;
                }
                tr:nth-child(odd) {
                    background: #f4f4f4;
                }
            </style>
            </head>
            <body>
                ' . $html . '
            </body>
            </html>';
    }
}
