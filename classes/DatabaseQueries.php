<?php

class DatabaseQueries extends Database
{
    private function processWhereClause(array $search)
    {
        $where = [];
        foreach ($search as $key => $value) {
            if ($key === 'playerId') {
                array_push($where, "roster.id = '" . $value . "'");
            }
            if ($key === 'player') {
                array_push($where, "roster.name = '" . $value . "'");
            }
            if ($key === 'team') {
                array_push($where, "roster.team_code = '" . $value . "'");
            }
            if ($key === 'position') {
                array_push($where, "roster.pos = '" . $value . "'");
            }
            if ($key === 'position') {
                array_push($where, "roster.nationality = '" . $value . "'");
            }
        }

        return implode(' AND ', $where);
    }

    public function executeQuery($query, $search)
    {
        return query($query . $this->processWhereClause($search)) ?? [];
    }
}
