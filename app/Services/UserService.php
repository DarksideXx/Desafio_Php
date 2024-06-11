<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UserService
{
    protected ValidatorFactory $validatorFactory;

    public function __construct(ValidatorFactory $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * Valida e cria um usuário.
     *
     * @param array $data Dados do usuário a serem validados e criados.
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateAndCreateUser(array $data): void
    {
        $validator = $this->validateUserData($data);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf_cnpj' => $data['cpf_cnpj'],
            'password' => bcrypt($data['password']),
            'type' => $data['type'], // Adicionando o campo 'type'
        ]);
    }

    /**
     * Valida os dados do usuário.
     *
     * @param array $data
     * @throws ValidationException
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateUserData(array $data): Validator
    {
        return $this->validatorFactory->make($data, User::$rules);
    }
}
