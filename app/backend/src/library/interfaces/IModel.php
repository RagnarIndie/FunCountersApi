<?php

namespace Test\Counters\Library\Interfaces;


interface IModel
{
    public function getTableName(): string;
    public function getClassName(): string;
    public function findAll(int $page = 1, int $limit = 50);
    public function findById(int $id);
    public function count(string $condition = '');
    public function query(string $query);
    public function columnAll(string $query);
    public function save();
}