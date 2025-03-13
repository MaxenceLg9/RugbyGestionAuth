<?php

enum Poste : string
{
    case PILIER = 'Pilier';
    case TALONNEUR = 'Talonneur';
    case DEUXIEME_LIGNE = "Deuxième ligne";
    case TROISIEME_LIGNE_AILE = "Troisième ligne aile";
    case TROISIEME_LIGNE_CENTRE = "Troisième ligne centre";
    case DEMI_MELEE = "Demi de mêlée";
    case DEMI_OUVERTURE = "Demi d'ouverture";
    case CENTRE = "Centre";
    case AILIER = "Ailier";
    case ARRIERE = "Arrière";

    public static function tryFromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null; // Return null if no match is found
    }
}