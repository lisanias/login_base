<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use PDOException;

class User extends DataLayer
{
    public function __construct()
    {
        parent::__construct("users", ["first_name", "last_name", "email", "passwd"]);
    }

    public function save(): bool
    {
        if (
            !$this->validateEmail()
            || !$this->validatePassword()
            || !parent::save()
        ) {
            return false;
        }

        return true;
    }

    protected function validateEmail(): bool
    {
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->fail = new PDOException(message:"Informe um e-mail válido");
            return false;
        }

        $userByEmail = null;
        if (!$this->id) {
            $userByEmail = $this->find(terms:"email = :email", params:"email={$this->email}")->count();
        } else {
            $userByEmail = $this->find(terms:"email = :email AND id != :id", params:"email={$this->email}&id={$this->id}")->count();
        }

        if ($userByEmail) {
            $this->fail = new PDOException(message:"O e-mail informado já está em uso");
            return false;
        }

        return true;
    }

    protected function validatePassword(): bool
    {
        if (empty($this->passwd) || strlen($this->passwd) < 5) {
            $this->fail = new PDOException(message:"Informe uma senha com pelo menos 5 caracteres");
            return false;
        }

        /** 
         * Verifica se já é uma senha criptografada
         */
        if (password_get_info($this->passwd)["algo"]) {
            return true;
        }

        $this->passwd = password_hash($this->passwd,algo:PASSWORD_DEFAULT);
        return true;
    }
}