<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Editor = 'editor';
    case Author = 'author';
    case Commercial = 'commercial';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Superadministrador',
            self::Admin => 'Administrador',
            self::Editor => 'Editor',
            self::Author => 'Autor',
            self::Commercial => 'Gestor comercial',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Controlo total da plataforma.',
            self::Admin => 'Gestão geral, conforme as permissões atribuídas.',
            self::Editor => 'Gestão de páginas, blog, portfólio e multimédia.',
            self::Author => 'Criação e edição dos próprios artigos.',
            self::Commercial => 'Gestão de contactos, propostas e newsletter.',
        };
    }

    /**
     * Permissions granted when the user has no custom permission set.
     *
     * @return list<Permission>
     */
    public function defaultPermissions(): array
    {
        return match ($this) {
            self::SuperAdmin => Permission::cases(),
            self::Admin => array_values(array_filter(
                Permission::cases(),
                fn (Permission $permission): bool => $permission !== Permission::ManageUsers,
            )),
            self::Editor => [
                Permission::ManagePages,
                Permission::ManageServices,
                Permission::ManageProjects,
                Permission::ManageClients,
                Permission::WritePosts,
                Permission::ManageAllPosts,
                Permission::PublishPosts,
                Permission::ManageTaxonomies,
                Permission::ManageMedia,
            ],
            self::Author => [
                Permission::WritePosts,
                Permission::ManageMedia,
            ],
            self::Commercial => [
                Permission::ManageLeads,
                Permission::ManageNewsletter,
            ],
        };
    }

    /**
     * Whether a super administrator may customise this role's permissions per user.
     */
    public function hasCustomizablePermissions(): bool
    {
        return $this === self::Admin;
    }
}
