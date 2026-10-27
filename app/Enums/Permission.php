<?php

namespace App\Enums;

enum Permission: string
{
    case ManagePages = 'manage-pages';
    case ManageServices = 'manage-services';
    case ManageProjects = 'manage-projects';
    case ManageClients = 'manage-clients';
    case WritePosts = 'write-posts';
    case ManageAllPosts = 'manage-all-posts';
    case PublishPosts = 'publish-posts';
    case ManageTaxonomies = 'manage-taxonomies';
    case ManageMedia = 'manage-media';
    case ManageLeads = 'manage-leads';
    case ManageNewsletter = 'manage-newsletter';
    case ManageUsers = 'manage-users';
    case ManageSettings = 'manage-settings';
    case ViewActivity = 'view-activity';

    public function label(): string
    {
        return match ($this) {
            self::ManagePages => 'Gerir páginas',
            self::ManageServices => 'Gerir serviços',
            self::ManageProjects => 'Gerir portfólio',
            self::ManageClients => 'Gerir clientes e parceiros',
            self::WritePosts => 'Escrever artigos próprios',
            self::ManageAllPosts => 'Gerir todos os artigos',
            self::PublishPosts => 'Publicar e agendar artigos',
            self::ManageTaxonomies => 'Gerir categorias e etiquetas',
            self::ManageMedia => 'Gerir biblioteca multimédia',
            self::ManageLeads => 'Gerir contactos e propostas',
            self::ManageNewsletter => 'Gerir newsletter',
            self::ManageUsers => 'Gerir utilizadores',
            self::ManageSettings => 'Gerir configurações',
            self::ViewActivity => 'Ver registo de actividades',
        };
    }
}
