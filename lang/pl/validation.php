<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Pole :attribute musi zostać zaakceptowane.',
    'accepted_if' => 'Pole :attribute musi zostać zaakceptowane gdy :other ma wartość :value.',
    'active_url' => 'Pole :attribute jest nieprawidłowym adresem URL.',
    'after' => 'Pole :attribute musi być datą późniejszą od :date.',
    'after_or_equal' => 'Pole :attribute musi być datą nie wcześniejszą niż :date.',
    'alpha' => 'Pole :attribute może zawierać jedynie litery.',
    'alpha_dash' => 'Pole :attribute może zawierać jedynie litery, cyfry i myślniki.',
    'alpha_num' => 'Pole :attribute może zawierać jedynie litery i cyfry.',
    'array' => 'Pole :attribute musi być tablicą.',
    'ascii' => 'Pole :attribute może zawierać tylko jednobajtowe znaki alfanumeryczne i symbole.',
    'before' => 'Pole :attribute musi być datą wcześniejszą od :date.',
    'before_or_equal' => 'Pole :attribute musi być datą nie późniejszą niż :date.',
    'between' => [
        'array' => 'Pole :attribute musi składać się z :min - :max elementów.',
        'file' => 'Pole :attribute musi zawierać się w granicach :min - :max kilobajtów.',
        'numeric' => 'Pole :attribute musi zawierać się w granicach :min - :max.',
        'string' => 'Pole :attribute musi zawierać się w granicach :min - :max znaków.',
    ],
    'boolean' => 'Pole :attribute musi mieć wartość prawda albo fałsz.',
    'can' => 'Pole :attribute zawiera niedozwoloną wartość.',
    'confirmed' => 'Potwierdzenie pola :attribute nie zgadza się.',
    'current_password' => 'Hasło jest nieprawidłowe.',
    'date' => 'Pole :attribute nie jest prawidłową datą.',
    'date_equals' => 'Pole :attribute musi być datą równą :date.',
    'date_format' => 'Pole :attribute nie jest w formacie :format.',
    'decimal' => 'Pole :attribute musi mieć :decimal miejsc po przecinku.',
    'declined' => 'Pole :attribute musi zostać odrzucone.',
    'declined_if' => 'Pole :attribute musi zostać odrzucone gdy :other ma wartość :value.',
    'different' => 'Pole :attribute oraz :other muszą się różnić.',
    'digits' => 'Pole :attribute musi składać się z :digits cyfr.',
    'digits_between' => 'Pole :attribute musi zawierać się w granicach :min - :max cyfr.',
    'dimensions' => 'Pole :attribute ma nieprawidłowe wymiary obrazu.',
    'distinct' => 'Pole :attribute ma zduplikowane wartości.',
    'doesnt_end_with' => 'Pole :attribute nie może kończyć się jednym z następujących: :values.',
    'doesnt_start_with' => 'Pole :attribute nie może zaczynać się jednym z następujących: :values.',
    'email' => 'Pole :attribute musi być prawidłowym adresem email.',
    'ends_with' => 'Pole :attribute musi kończyć się jednym z następujących: :values.',
    'enum' => 'Wybrana wartość dla :attribute jest nieprawidłowa.',
    'exists' => 'Wybrana wartość dla :attribute jest nieprawidłowa.',
    'file' => 'Pole :attribute musi być plikiem.',
    'filled' => 'Pole :attribute nie może być puste.',
    'gt' => [
        'array' => 'Pole :attribute musi mieć więcej niż :value elementów.',
        'file' => 'Pole :attribute musi być większe niż :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być większe niż :value.',
        'string' => 'Pole :attribute musi być dłuższe niż :value znaków.',
    ],
    'gte' => [
        'array' => 'Pole :attribute musi mieć :value lub więcej elementów.',
        'file' => 'Pole :attribute musi być większe lub równe :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być większe lub równe :value.',
        'string' => 'Pole :attribute musi być dłuższe lub równe :value znaków.',
    ],
    'image' => 'Pole :attribute musi być obrazem.',
    'in' => 'Wybrana wartość dla :attribute jest nieprawidłowa.',
    'in_array' => 'Pole :attribute nie istnieje w :other.',
    'integer' => 'Pole :attribute musi być liczbą całkowitą.',
    'ip' => 'Pole :attribute musi być prawidłowym adresem IP.',
    'ipv4' => 'Pole :attribute musi być prawidłowym adresem IPv4.',
    'ipv6' => 'Pole :attribute musi być prawidłowym adresem IPv6.',
    'json' => 'Pole :attribute musi być prawidłowym ciągiem znaków JSON.',
    'lowercase' => 'Pole :attribute musi być napisane małymi literami.',
    'lt' => [
        'array' => 'Pole :attribute musi mieć mniej niż :value elementów.',
        'file' => 'Pole :attribute musi być mniejsze niż :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być mniejsze niż :value.',
        'string' => 'Pole :attribute musi być krótsze niż :value znaków.',
    ],
    'lte' => [
        'array' => 'Pole :attribute nie może mieć więcej niż :value elementów.',
        'file' => 'Pole :attribute musi być mniejsze lub równe :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być mniejsze lub równe :value.',
        'string' => 'Pole :attribute musi być krótsze lub równe :value znaków.',
    ],
    'mac_address' => 'Pole :attribute musi być prawidłowym adresem MAC.',
    'max' => [
        'array' => 'Pole :attribute nie może mieć więcej niż :max elementów.',
        'file' => 'Pole :attribute nie może być większe niż :max kilobajtów.',
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'string' => 'Pole :attribute nie może być dłuższe niż :max znaków.',
    ],
    'max_digits' => 'Pole :attribute nie może mieć więcej niż :max cyfr.',
    'mimes' => 'Pole :attribute musi być plikiem typu: :values.',
    'mimetypes' => 'Pole :attribute musi być plikiem typu: :values.',
    'min' => [
        'array' => 'Pole :attribute musi mieć minimum :min elementów.',
        'file' => 'Pole :attribute musi mieć minimum :min kilobajtów.',
        'numeric' => 'Pole :attribute musi być większe od :min.',
        'string' => 'Pole :attribute musi mieć minimum :min znaków.',
    ],
    'min_digits' => 'Pole :attribute musi mieć minimum :min cyfr.',
    'missing' => 'Pole :attribute musi być nieobecne.',
    'missing_if' => 'Pole :attribute musi być nieobecne gdy :other ma wartość :value.',
    'missing_unless' => 'Pole :attribute musi być nieobecne, chyba że :other ma wartość :value.',
    'missing_with' => 'Pole :attribute musi być nieobecne gdy :values jest obecne.',
    'missing_with_all' => 'Pole :attribute musi być nieobecne gdy :values są obecne.',
    'multiple_of' => 'Pole :attribute musi być wielokrotnością :value.',
    'not_in' => 'Wybrana wartość dla :attribute jest nieprawidłowa.',
    'not_regex' => 'Format pola :attribute jest nieprawidłowy.',
    'numeric' => 'Pole :attribute musi być liczbą.',
    'password' => [
        'letters' => 'Pole :attribute musi zawierać przynajmniej jedną literę.',
        'mixed' => 'Pole :attribute musi zawierać przynajmniej jedną wielką i jedną małą literę.',
        'numbers' => 'Pole :attribute musi zawierać przynajmniej jedną liczbę.',
        'symbols' => 'Pole :attribute musi zawierać przynajmniej jeden symbol.',
        'uncompromised' => 'Podana wartość pola :attribute pojawiła się w wycieku danych. Proszę wybrać inną wartość :attribute.',
    ],
    'present' => 'Pole :attribute musi być obecne.',
    'prohibited' => 'Pole :attribute jest zabronione.',
    'prohibited_if' => 'Pole :attribute jest zabronione gdy :other ma wartość :value.',
    'prohibited_unless' => 'Pole :attribute jest zabronione, chyba że :other ma wartość :values.',
    'prohibits' => 'Pole :attribute zabrania obecności :other.',
    'regex' => 'Format pola :attribute jest nieprawidłowy.',
    'required' => 'Pole :attribute jest wymagane.',
    'required_array_keys' => 'Pole :attribute musi zawierać wartości dla kluczy: :values.',
    'required_if' => 'Pole :attribute jest wymagane gdy :other ma wartość :value.',
    'required_if_accepted' => 'Pole :attribute jest wymagane gdy :other jest zaakceptowane.',
    'required_unless' => 'Pole :attribute jest wymagane jeżeli :other nie znajduje się w :values.',
    'required_with' => 'Pole :attribute jest wymagane gdy :values jest obecne.',
    'required_with_all' => 'Pole :attribute jest wymagane gdy wszystkie :values są obecne.',
    'required_without' => 'Pole :attribute jest wymagane gdy :values nie jest obecne.',
    'required_without_all' => 'Pole :attribute jest wymagane gdy żadne z :values nie są obecne.',
    'same' => 'Pole :attribute i :other muszą być takie same.',
    'size' => [
        'array' => 'Pole :attribute musi zawierać :size elementów.',
        'file' => 'Pole :attribute musi mieć :size kilobajtów.',
        'numeric' => 'Pole :attribute musi mieć :size.',
        'string' => 'Pole :attribute musi mieć :size znaków.',
    ],
    'starts_with' => 'Pole :attribute musi zaczynać się jednym z następujących: :values.',
    'string' => 'Pole :attribute musi być ciągiem znaków.',
    'timezone' => 'Pole :attribute musi być prawidłową strefą czasową.',
    'unique' => 'Ta wartość pola :attribute jest już zajęta.',
    'uploaded' => 'Nie udało się przesłać pliku :attribute.',
    'uppercase' => 'Pole :attribute musi być napisane wielkimi literami.',
    'url' => 'Format pola :attribute jest nieprawidłowy.',
    'ulid' => 'Pole :attribute musi być prawidłowym ULID.',
    'uuid' => 'Pole :attribute musi być prawidłowym identyfikatorem UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'adres email',
        'password' => 'hasło',
        'name' => 'nazwa',
        'first_name' => 'imię',
        'last_name' => 'nazwisko',
        'phone' => 'telefon',
        'address' => 'adres',
        'city' => 'miasto',
        'country' => 'kraj',
        'postal_code' => 'kod pocztowy',
    ],

]; 