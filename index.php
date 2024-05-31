<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

#Объединение ФИО
function getFullnameFromParts($surname, $name, $middleName) {
    return $fullname = $surname . ' ' . $name . ' ' . $middleName;
};

#Разбиение ФИО
function getPartsFromFullname($fullname) {
    $fullnameKeys = ['surname', 'name', 'middleName'];
    return array_combine($fullnameKeys, explode(' ', $fullname));
};

#Сокращение ФИО
function getShortName($fullname) {
    $fullnameArray = getPartsFromFullname($fullname);
    return $fullnameArray['name'] . ' ' . mb_substr($fullnameArray['surname'], 0, 1) . '.';
};

#Определение пола
function getGenderFromName($fullname) {
    $fullnameArray = getPartsFromFullname($fullname);
    $genderScore = 0;

    #Получаем признак пола по отчеству
    if (mb_substr($fullnameArray['middleName'], -3) == 'вна') {
        $genderScore--;
    } elseif (mb_substr($fullnameArray['middleName'], -2) == 'ич') {
        $genderScore++;
    };

    #Получаем признак пола по имени
    if (mb_substr($fullnameArray['name'], -1) == 'а') {
        $genderScore--;
    } elseif (mb_substr($fullnameArray['name'], -1) == 'й' || mb_substr($fullnameArray['name'], -1) == 'н') {
        $genderScore++;
    };

    #Получаем признак пола по фамилии
    if (mb_substr($fullnameArray['surname'], -2) == 'ва') {
        $genderScore--;
    } elseif (mb_substr($fullnameArray['surname'], -1) == 'в') {
        $genderScore++;
    };

    return $genderScore <=> 0;
};

#Определение возрастно-полового состава
function getGenderDescription($array) {
    function genderCounter ($element) { 
        return getGenderFromName($element['fullname']);
    };

    $b = array_map('genderCounter', $array);
    $c = array_count_values($b);

    bcscale(2);
    $males = bcdiv($c['1'] * 100, count($b));
    $females = bcdiv($c['-1'] * 100, count($b));
    $unknown = bcdiv($c['0'] * 100, count($b));

    echo "Гендерный состав аудитории:\n";
    echo "Мужчины -$males%\n";
    echo "Женщины - $females%\n";
    echo "Не удалось определить - $unknown%";
};

#Идеальный подбор пары
function getPerfectPartner($surname, $name, $middleName, $array) {
    $surname = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $middleName = mb_convert_case($middleName, MB_CASE_TITLE_SIMPLE);
    $fullname = getFullnameFromParts($surname, $name, $middleName);
    $userGender = getGenderFromName($fullname);

    function getPartner($array, $userGender) {
        $partner = $array[rand(0, count($array))]['fullname'];

        if (getGenderFromName($partner) != $userGender) {
            return $partner;
        } else {
            return getPartner($array, $userGender);
        };
    };

    if($userGender != 0) {
        $partner = getShortName(getPartner($array, $userGender));
        $user = getShortName($fullname);
        $x = rand(5000, 10000)/100;

        echo "$user + $partner = ♡ Идеально на $x% ♡";

    } else echo "Не удалось подобрать пару \u{1F613}";
};

?>