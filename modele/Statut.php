<?php
    enum Statut: String {

        case ACTIF = 'ACTIF';
        case BLESSE = 'BLESSE';
        case SUSPENDU = 'SUSPENDU';
        case ABSENT = 'ABSENT';

        public static function tryFromName(string $name): ?self {
            foreach (self::cases() as $case) {
                if ($case->name === $name) {
                    return $case;
                }
            }
            return null; // Return null if no match is found
        }

    }

?>