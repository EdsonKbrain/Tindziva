<?php

namespace App\Enums;

/**
 * Client groups used in the Tindziva portfolio.
 */
enum ClientSegment: string
{
    case SmallBusiness = 'small_business';
    case LargeCompany = 'large_company';
    case Government = 'government';
    case PublicCompany = 'public_company';
    case CivilSociety = 'civil_society';
    case International = 'international';
    case ProjectsAndBrands = 'projects_brands';

    public function label(): string
    {
        return match ($this) {
            self::SmallBusiness => 'Micro, pequenas e médias empresas',
            self::LargeCompany => 'Grandes empresas',
            self::Government => 'Governo',
            self::PublicCompany => 'Empresas públicas',
            self::CivilSociety => 'Organizações da sociedade civil',
            self::International => 'Instituições de cooperação internacional',
            self::ProjectsAndBrands => 'Projectos e marcas',
        };
    }
}
