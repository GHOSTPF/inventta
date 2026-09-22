<?php

return [
    'accepted' => 'O campo :attribute deve ser aceito.',
    'confirmed' => 'A confirmação do campo :attribute não confere.',
    'current_password' => 'A senha está incorreta.',
    'date' => 'O campo :attribute não é uma data válida.',
    'email' => 'O campo :attribute deve ser um e-mail válido.',
    'exists' => 'O :attribute selecionado é inválido.',
    'in' => 'O :attribute selecionado é inválido.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'lowercase' => 'O campo :attribute deve estar em minúsculas.',
    'max' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],
    'numeric' => 'O campo :attribute deve ser um número.',
    'password' => [
        'letters' => 'O campo :attribute deve conter ao menos uma letra.',
        'mixed' => 'O campo :attribute deve conter letras maiúsculas e minúsculas.',
        'numbers' => 'O campo :attribute deve conter ao menos um número.',
        'symbols' => 'O campo :attribute deve conter ao menos um símbolo.',
    ],
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',
    'unique' => 'Já existe um registro com este :attribute.',

    'custom' => [],

    'attributes' => [
        'name' => 'nome',
        'sku' => 'SKU',
        'category_id' => 'categoria',
        'cost_price' => 'preço de custo',
        'sale_price' => 'preço de venda',
        'quantity' => 'quantidade',
        'min_quantity' => 'estoque mínimo',
        'product_id' => 'produto',
        'type' => 'tipo',
        'reason' => 'motivo',
        'unit_price' => 'valor unitário',
        'notes' => 'observações',
        'email' => 'e-mail',
        'password' => 'senha',
    ],
];
