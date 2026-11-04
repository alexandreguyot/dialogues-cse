<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const SLUGS = ['mentions-legales', 'politique-de-confidentialite', 'conditions-generales'];

    public function up(): void
    {
        $this->insert('Mentions légales', 'mentions-legales', true, 'Éditeur, directeur de la publication et hébergeur du site Dialogues CSE.', [
            'Éditeur du site' => "DIALOGUES, société par actions simplifiée au capital de 1 000 €\nSiège social : 4 Villa des Rosettes, 94120 Fontenay-sous-Bois\nSIREN : 106 669 393\nSIRET du siège : 106 669 393 00014\nTVA intracommunautaire : FR50106669393\nContact : bonjourdialogues@gmail.com",
            'Directeur de la publication' => 'Maxence Defrance, président.',
            'Hébergeur' => "OVH SAS\n2 rue Kellermann, 59100 Roubaix, France\nTéléphone : 1007",
            'Propriété intellectuelle' => "Les textes, illustrations, logos et autres contenus de ce site sont protégés par le droit de la propriété intellectuelle. Toute reproduction ou réutilisation sans l'accord écrit de DIALOGUES est interdite.",
            'Données personnelles' => 'Le traitement de vos données est décrit dans la page « Politique de confidentialité », accessible en bas de chaque page du site.',
        ]);

        $this->insert('Politique de confidentialité', 'politique-de-confidentialite', true, 'Comment Dialogues CSE traite les données transmises par le formulaire de devis, et quels sont vos droits.', [
            'Responsable du traitement' => "DIALOGUES, 4 Villa des Rosettes, 94120 Fontenay-sous-Bois.\nContact : bonjourdialogues@gmail.com",
            'Données collectées' => "Le formulaire de demande de devis recueille votre nom et prénom, votre adresse e-mail, votre message et, si vous les indiquez, votre téléphone, votre entreprise et la date à laquelle vous joindre.",
            'Finalité et base légale' => "Ces données servent uniquement à répondre à votre demande et à préparer un devis. Ce traitement repose sur les mesures préalables à un contrat que vous sollicitez et sur notre intérêt légitime à répondre aux personnes qui nous contactent.",
            'Destinataires' => "Votre demande est transmise par e-mail à DIALOGUES et au prestataire technique du site. Interviennent aussi OVH SAS, pour l'hébergement et l'envoi des e-mails, et Google, pour la messagerie Gmail qui les reçoit. Vos données ne sont ni vendues ni utilisées à des fins publicitaires.",
            'Durée de conservation' => 'Vos demandes sont conservées 3 ans à compter de notre dernier échange, sauf si elles donnent lieu à un contrat, dont la conservation suit alors les obligations légales.',
            'Cookies et services tiers' => "Le site dépose deux cookies techniques, nécessaires à son fonctionnement et à la sécurité des formulaires, valables 2 heures.\nLes polices de caractères sont chargées depuis Google Fonts, ce qui transmet votre adresse IP à Google.\nLa page de demande de devis utilise Google reCAPTCHA pour bloquer les envois automatisés. Ce service peut déposer des cookies et recueillir des données techniques sur votre navigation.\nGoogle peut traiter ces données aux États-Unis.\nLe site n'utilise aucun outil de statistiques ni de publicité.",
            'Vos droits' => "Vous pouvez demander l'accès à vos données, leur rectification, leur effacement, leur portabilité ou la limitation de leur traitement, et vous opposer à celui-ci, en écrivant à bonjourdialogues@gmail.com. Vous pouvez aussi adresser une réclamation à la CNIL (www.cnil.fr).",
        ], 'Vos données personnelles');

        // Brouillon : les conditions commerciales marquées [À COMPLÉTER] doivent être décidées par DIALOGUES avant publication.
        $this->insert('Conditions générales', 'conditions-generales', false, 'Conditions générales applicables aux prestations de Dialogues CSE.', [
            'Objet' => "Les présentes conditions s'appliquent aux prestations de communication et d'accompagnement des CSE proposées par DIALOGUES, SAS au capital de 1 000 €, SIREN 106 669 393, 4 Villa des Rosettes, 94120 Fontenay-sous-Bois.",
            'Devis et commande' => "Chaque prestation fait l'objet d'un devis. La commande est ferme à la réception du devis signé. Durée de validité des devis : [À COMPLÉTER].",
            'Prix et paiement' => "Les prix sont ceux indiqués au devis. Acompte à la commande : [À COMPLÉTER]. Délai de paiement : [À COMPLÉTER]. Pénalités de retard et indemnité de recouvrement : [À COMPLÉTER, selon que le client est un professionnel ou un non-professionnel].",
            'Annulation et modification' => '[À COMPLÉTER].',
            'Propriété intellectuelle' => 'Droits cédés sur les créations livrées, étendue et moment de la cession : [À COMPLÉTER].',
            'Responsabilité' => '[À COMPLÉTER].',
            'Données personnelles' => 'Voir la page « Politique de confidentialité ». Rôle de DIALOGUES pour les données confiées par le CSE dans le cadre des missions : [À COMPLÉTER].',
            'Droit applicable et litiges' => 'Les présentes conditions sont soumises au droit français. Tribunal compétent et médiation éventuelle : [À COMPLÉTER].',
        ]);
    }

    public function down(): void
    {
        DB::table('pages')->whereIn('slug', self::SLUGS)->delete();
    }

    // $heading : « confidentialité » déborde en grand titre sur mobile.
    private function insert(string $title, string $slug, bool $published, string $description, array $sections, ?string $heading = null): void
    {
        $blocks = [['id' => Str::random(8), 'type' => 'hero', 'title' => $heading ?? $title, 'text' => '', 'button_label' => '', 'button_url' => '']];

        foreach ($sections as $sectionTitle => $text) {
            $blocks[] = ['id' => Str::random(8), 'type' => 'text', 'title' => $sectionTitle, 'text' => $text, 'image' => ''];
        }

        DB::table('pages')->insertOrIgnore([
            'title'            => $title,
            'slug'             => $slug,
            'theme'            => 'light',
            'meta_description' => $description,
            'in_menu'          => false,
            'is_published'     => $published,
            'blocks'           => json_encode($blocks, JSON_UNESCAPED_UNICODE),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }
};
