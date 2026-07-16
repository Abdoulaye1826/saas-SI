<?php

namespace App\Enums;

/**
 * Catégories des écritures de trésorerie (entrées et sorties). Chaque
 * catégorie n'est valable que dans un sens (entrée ou sortie), sauf
 * "virement" qui sert aux deux jambes d'un virement interne.
 */
enum FinancialCategory: string
{
    // ── Entrées ──
    case Vente = 'vente';
    case PaiementFacture = 'paiement_facture';
    case PaiementComplementaire = 'paiement_complementaire';
    case Echange = 'echange';
    case AvanceClient = 'avance_client';
    case ApportProprietaire = 'apport_proprietaire';
    case RemboursementRecu = 'remboursement_recu';
    case AutreRecette = 'autre_recette';

    // ── Sorties ──
    case AchatMarchandise = 'achat_marchandise';
    case Fournisseur = 'fournisseur';
    case Loyer = 'loyer';
    case Electricite = 'electricite';
    case Eau = 'eau';
    case Internet = 'internet';
    case Salaires = 'salaires';
    case Transport = 'transport';
    case Reparation = 'reparation';
    case Maintenance = 'maintenance';
    case Publicite = 'publicite';
    case Impots = 'impots';
    case Fournitures = 'fournitures';
    case Divers = 'divers';
    case AvanceFournisseur = 'avance_fournisseur';

    // ── Les deux sens (virement interne) ──
    case Virement = 'virement';

    public function label(): string
    {
        return match ($this) {
            self::Vente => 'Vente',
            self::PaiementFacture => 'Paiement facture',
            self::PaiementComplementaire => 'Paiement complémentaire',
            self::Echange => 'Échange',
            self::AvanceClient => 'Avance client',
            self::ApportProprietaire => 'Apport propriétaire',
            self::RemboursementRecu => 'Remboursement reçu',
            self::AutreRecette => 'Autre recette',
            self::AchatMarchandise => 'Achat marchandise',
            self::Fournisseur => 'Fournisseur',
            self::Loyer => 'Loyer',
            self::Electricite => 'Électricité',
            self::Eau => 'Eau',
            self::Internet => 'Internet',
            self::Salaires => 'Salaires',
            self::Transport => 'Transport',
            self::Reparation => 'Réparation',
            self::Maintenance => 'Maintenance',
            self::Publicite => 'Publicité',
            self::Impots => 'Impôts',
            self::Fournitures => 'Fournitures',
            self::Divers => 'Divers',
            self::AvanceFournisseur => 'Avance fournisseur',
            self::Virement => 'Virement interne',
        };
    }

    /**
     * Sens (entrée/sortie/les deux) autorisé(s) pour cette catégorie —
     * utilisé pour valider qu'une catégorie de sortie n'est pas choisie
     * sur une entrée et inversement.
     *
     * @return array<FinancialTransactionType>
     */
    public function allowedTypes(): array
    {
        return match ($this) {
            self::Vente, self::PaiementFacture, self::PaiementComplementaire,
            self::Echange, self::AvanceClient, self::ApportProprietaire,
            self::RemboursementRecu, self::AutreRecette
                => [FinancialTransactionType::In],

            self::Virement
                => [FinancialTransactionType::In, FinancialTransactionType::Out],

            default => [FinancialTransactionType::Out],
        };
    }

    /**
     * @return array<self>
     */
    public static function forType(FinancialTransactionType $type): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $category) => in_array($type, $category->allowedTypes(), true)
        ));
    }
}
