<?php

namespace Core\Database;

/**
 * Représente une table en base de données.
 */
class Table
{
    /**
     * @var Database
     */
    protected $database;

    /**
     * Nom de la table en abse de données.
     */
    protected const TABLE = null;

    /**
     * Permet de définir dans quel entité sauvegarder les résultats.
     */
    protected const ENTITY = null;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Récupère un enregistrement en se basant sur l'ID.
     *
     * @param int $id
     *
     * @return \stdClass
     */
    public function find(int $id)
    {
        return $this->database->fetch('SELECT * FROM ' . static::TABLE . ' WHERE id = ?', [$id], static::ENTITY);
    }

    /**
     * écupère un enregistrement en se basant sur l'ID et renvoie une exception si l'entité n'existe pas.
     *
     * @param $id
     *
     * @throws NoRecordException
     *
     * @return \stdClass
     */
    public function findOrFail(int $id)
    {
        $record = $this->find($id);
        if (!$record) {
            throw new NoRecordException('Aucun enregistrement ' . static::TABLE . '::' . $id);
        }

        return $record;
    }

    /**
     * Supprime un enregistrement.
     *
     * @param int $id
     *
     * @return \PDOStatement
     */
    public function delete(int $id): \PDOStatement
    {
        return $this->database->query('DELETE FROM ' . static::TABLE . ' WHERE id = ?', [$id]);
    }

    /**
     * Met à jour un enregistrement
     * Attention, les clefs ne sont pas échapées !
     *
     * @param int   $id
     * @param array $params
     *
     * @return \PDOStatement
     */
    public function update(int $id, array $params): \PDOStatement
    {
        $query = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
        $params['id'] = $id;

        return $this->database->query('UPDATE ' . static::TABLE . ' SET ' . $query . ' WHERE id = :id', $params);
    }

    /**
     * Crée un nouvel enregistrement.
     *
     * @param array $params
     *
     * @return int|null
     */
    public function create(array $params): ?int
    {
        $query = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
        $this->database->query('INSERT INTO ' . static::TABLE . ' SET ' . $query, $params);

        return $this->database->lastInsertId();
    }

    /**
     * Compte le nombre d'enregistrement.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->database->fetchColumn('SELECT COUNT(id) FROM ' . static::TABLE);
    }
}
