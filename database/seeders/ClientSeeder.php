<?php

namespace Database\Seeders;

use App\Enums\ClientSegment;
use App\Enums\ClientType;
use App\Models\Client;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Clients and partners listed in the Tindziva 2024 portfolio, with their logos.
 */
class ClientSeeder extends Seeder
{
    private const LOGO_PATH = __DIR__.'/assets/clients';

    /**
     * @var array<string, Media>
     */
    private array $uploadedLogos = [];

    public function run(MediaService $media): void
    {
        foreach ($this->clients() as $position => [$name, $segment, $services, $logo]) {
            $client = Client::firstOrNew(['slug' => Str::slug($name)]);

            if ($client->exists) {
                continue;
            }

            $client->fill([
                'name' => $name,
                'type' => $segment === ClientSegment::International ? ClientType::Partner : ClientType::Client,
                'segment' => $segment,
                'services_provided' => $services ?: null,
                'logo_id' => $logo ? $this->logo($media, $logo, $name)?->id : null,
                'sort_order' => $position,
                'is_visible' => true,
            ])->save();
        }
    }

    private function logo(MediaService $media, string $file, string $name): ?Media
    {
        $path = self::LOGO_PATH."/{$file}.png";

        if (! is_file($path)) {
            return null;
        }

        return $this->uploadedLogos[$file] ??= $media->store(
            new UploadedFile($path, "logotipo-{$file}.png", 'image/png', null, true),
            ['title' => "Logótipo {$name}", 'alt_text' => "Logótipo {$name}"],
        );
    }

    /**
     * @return list<array{0: string, 1: ClientSegment, 2: list<string>, 3: string|null}>
     */
    private function clients(): array
    {
        $sme = ClientSegment::SmallBusiness;
        $civil = ClientSegment::CivilSociety;
        $projects = ClientSegment::ProjectsAndBrands;
        $content = ['Criação de conteúdo', 'Produção textual', 'Revisão editorial e linguística'];

        return [
            ['GIZ — Cooperação Alemã para o Desenvolvimento', ClientSegment::International, ['Produção audiovisual', 'Live streaming', 'Criação de website'], 'giz'],
            ['PNUD — Fórum de Monitoria do Mecanismo de Revisão Periódica Universal dos Direitos Humanos', ClientSegment::International, ['Assessoria de imprensa'], 'pnud'],
            ['Centro Cultural Moçambicano-Alemão', ClientSegment::International, ['Relações públicas', 'Assessoria de imprensa'], 'ccma'],
            ['EMOSE — Empresa Moçambicana de Seguros', ClientSegment::PublicCompany, ['Assessoria de imprensa', 'Produção audiovisual'], 'emose'],
            ['CPMZ — Companhia do Pipeline Moçambique-Zimbabwe', ClientSegment::LargeCompany, ['Impressão gráfica'], 'cpmz'],
            ['Ministério da Indústria e Comércio', ClientSegment::Government, ['Consultoria em comunicação e imagem'], 'republica-de-mocambique'],
            ['Ministério do Trabalho e Segurança Social', ClientSegment::Government, ['Produção de eventos', 'Animação cultural (teatro)'], 'republica-de-mocambique'],
            ['Conselho Municipal da Matola', ClientSegment::Government, ['Inserção de publicidade nas TV'], 'conselho-municipal-da-matola'],
            ['BDQ Holding', $sme, ['Assessoria de imprensa', 'Produção audiovisual'], 'bdq-holding'],
            ['Evolution', $sme, ['Assessoria de imprensa', 'Produção audiovisual', 'Cobertura fotográfica', 'Produção de eventos', 'Mestre de cerimónias'], 'evolution'],
            ['Security 2U', $sme, ['Relações públicas', 'Assessoria de imprensa', 'Marketing digital', 'Design gráfico', 'Produção audiovisual'], 'security-2u'],
            ['Vector Musik', $sme, ['Relações públicas', 'Assessoria de imprensa', 'Marketing digital'], 'vector-musik'],
            ['Sol Solutions & Services', $sme, ['Relações públicas', 'Assessoria de imprensa', 'Marketing digital'], 'sol-solutions'],
            ['GM Record & Services', $sme, ['Relações públicas', 'Assessoria de imprensa', 'Edição e revisão', 'Revisão linguística'], 'gm-record-services'],
            ["P'Larte D'Alma", $sme, ['Relações públicas', 'Assessoria de imprensa', 'Produção de eventos', 'Mestre de cerimónias'], 'plarte-dalma'],
            ['Tecnotraguas', $sme, ['Produção audiovisual'], 'tecnotraguas'],
            ['Touch Publicidade', $sme, ['Relações públicas', 'Assessoria de imprensa'], 'touch-publicidade'],
            ['Zwela Moçambique', $sme, ['Relações públicas', 'Assessoria de imprensa'], 'zwela-mocambique'],
            ['Mechanical Tecnologia', $sme, [], 'mechanical-tecnologia'],
            ['Tipografia Prelo Clássico', $sme, [], 'tipografia-prelo-classico'],
            ['Ginho Sibia Events & Artists Management', $sme, [], 'ginho-sibia'],
            ['ACIS', $civil, ['Produção audiovisual', 'Assessoria de imprensa', 'Produção de eventos', 'Publicidade', 'Branding e impressão gráfica'], 'acis'],
            ['Bloco 4 Foundation', $civil, ['Produção audiovisual', 'Assessoria de imprensa', 'Produção de eventos', 'Design gráfico'], 'bloco-4-foundation'],
            ['Associação Cultural Nkaringanarte', $civil, ['Relações públicas', 'Produção de eventos', 'Gestão de redes sociais'], 'nkaringanarte'],
            ["Rural Women's Assembly", $civil, [], 'rural-womens-assembly'],
            ['Muendarte', $civil, [], 'muendarte'],
            ['Fórum Mulher', $civil, [], 'forum-mulher'],
            ['Entre Aspas', $projects, ['Estratégia de comunicação', 'Consultoria de marketing', 'Marketing digital', 'Design gráfico e produção audiovisual', 'Impressão gráfica'], 'entre-aspas'],
            ['Kuvaninga', $projects, $content, null],
            ['Revista Mais Jovem', $projects, $content, 'mais-jovem'],
            ['Revista Popotio', $projects, ['Criação de conteúdo', 'Edição', 'Produção textual', 'Revisão editorial e linguística'], 'popotio'],
            ['Revista Portal Diplomático', $projects, ['Criação de conteúdo', 'Edição', 'Produção textual', 'Revisão editorial e linguística'], 'portal-diplomatico'],
            ['Jornal Evidências', $projects, ['Criação de conteúdo', 'Produção textual', 'Notícias culturais'], 'jornal-evidencias'],
            ['Matola Sundown in the Garden', $projects, [], 'matola-sundown'],
            ['Poetas d’Alma', $projects, [], 'poetas-dalma'],
            ['Fauziya Fliege', $projects, [], 'fauziya-fliege'],
            ['Natal du Tau', $projects, [], 'natal-du-tau'],
            ['Be Like a Woman', $projects, [], 'be-like-a-woman'],
            ['Fancy Africa', $projects, [], null],
        ];
    }
}
