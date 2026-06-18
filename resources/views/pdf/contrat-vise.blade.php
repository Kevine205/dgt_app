<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #1a1a1a; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #166534; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { font-size: 16pt; font-weight: bold; color: #166534; margin: 5px 0; }
        .header h2 { font-size: 13pt; color: #374151; margin: 0; }
        .visa-badge { background: #dcfce7; border: 2px solid #166534; border-radius: 8px; padding: 10px 20px; text-align: center; margin: 15px 0; }
        .visa-badge h3 { color: #166534; font-size: 13pt; margin: 0 0 4px; }
        .visa-badge p { color: #15803d; margin: 0; font-size: 10pt; }
        .section { margin-bottom: 18px; }
        .section h4 { font-size: 10pt; font-weight: bold; color: #1e3a5f; border-bottom: 1px solid #93c5fd; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 8px; font-size: 10pt; }
        td:first-child { color: #6b7280; width: 42%; }
        td:last-child { font-weight: 600; color: #111827; }
        .footer { margin-top: 30px; border-top: 2px solid #166534; padding-top: 15px; }
        .signatures { display: table; width: 100%; }
        .sig-box { display: table-cell; width: 50%; text-align: center; padding: 10px; vertical-align: bottom; }
        .sig-label { font-size: 9pt; color: #374151; font-weight: bold; margin-bottom: 5px; }
        .sig-date { font-size: 9pt; color: #6b7280; margin-top: 5px; }
        .sig-line { border-top: 1px solid #374151; margin: 8px 20px 5px; }
        .sig-image { max-height: 70px; max-width: 200px; margin: 0 auto 5px; display: block; }
        .numero { background: #f0fdf4; border: 1px solid #86efac; padding: 6px 15px; border-radius: 6px; font-family: monospace; font-size: 12pt; font-weight: bold; color: #166534; }
        .watermark { color: #9ca3af; font-size: 8pt; text-align: center; margin-top: 15px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
<div class="header">
    <h1>RÉPUBLIQUE DU BÉNIN</h1>
    <p style="color:#6b7280;font-size:9pt;margin:2px 0">Ministère du Travail et de la Fonction Publique</p>
    <h2>DIRECTION GÉNÉRALE DU TRAVAIL</h2>
    <p style="font-size:9pt;color:#374151;margin-top:4px">Cotonou — Bénin</p>
</div>

<div style="text-align:center;margin-bottom:15px;">
    <span class="numero">{{ $dossier->numero_suivi }}</span>
</div>

@php $dateVisa = $dossier->date_visa ?? now(); @endphp

<div class="visa-badge">
    <h3>✓ CONTRAT DE TRAVAIL VISÉ</h3>
    <p>Examiné et visé conformément aux dispositions du Code du Travail béninois</p>
    <p style="margin-top:4px;font-weight:600;">Date de visa : {{ $dateVisa->format('d/m/Y à H:i') }}</p>
</div>

<div class="section">
    <h4>Informations de l'employeur</h4>
    <table>
        <tr><td>Raison sociale</td><td>{{ $dossier->nom_employeur }}</td></tr>
        <tr><td>Secteur d'activité</td><td>{{ $dossier->secteur_activite ?? '—' }}</td></tr>
        <tr><td>Adresse</td><td>{{ $dossier->adresse_employeur ?? '—' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Informations de l'employé</h4>
    <table>
        <tr><td>Nom et prénom</td><td>{{ $dossier->prenom_employe }} {{ $dossier->nom_employe }}</td></tr>
        <tr><td>Date de naissance</td><td>{{ $dossier->date_naissance_employe?->format('d/m/Y') ?? '—' }}</td></tr>
        <tr><td>Nationalité</td><td>{{ $dossier->nationalite_employe ?? '—' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Caractéristiques du contrat</h4>
    <table>
        <tr><td>Type de contrat</td><td>{{ $dossier->type_contrat }}</td></tr>
        <tr><td>Poste occupé</td><td>{{ $dossier->poste }}</td></tr>
        <tr><td>Date de signature</td><td>{{ $dossier->date_signature->format('d/m/Y') }}</td></tr>
        <tr><td>Date de prise d'effet</td><td>{{ $dossier->date_debut->format('d/m/Y') }}</td></tr>
        @if($dossier->date_fin)
        <tr><td>Date d'échéance</td><td>{{ $dossier->date_fin->format('d/m/Y') }}</td></tr>
        @endif
        @if($dossier->salaire)
        <tr><td>Salaire mensuel</td><td>{{ number_format($dossier->salaire, 0, ',', ' ') }} FCFA</td></tr>
        @endif
    </table>
</div>

<div class="footer">
    <div class="signatures">
        <div class="sig-box">
            <div class="sig-label">L'Employeur</div>
            <br><br><br>
            <div class="sig-line"></div>
            <div>{{ $dossier->nom_employeur }}</div>
        </div>
        <div class="sig-box">
            <div class="sig-label">Le Directeur Général du Travail<br>ou son représentant habilité</div>
            @if(isset($validateur) && $validateur && $validateur->signature_electronique)
                <img src="{{ $validateur->signature_electronique }}" class="sig-image" alt="Signature">
            @else
                <br><br><br>
            @endif
            <div class="sig-line"></div>
            <div>{{ isset($validateur) && $validateur ? $validateur->nom_complet : 'DGT Bénin' }}</div>
            <div class="sig-date">Visé le {{ $dateVisa->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

<div class="watermark">
    Document officiel — DGT Bénin — Réf. {{ $dossier->numero_suivi }} — {{ $dateVisa->format('d/m/Y H:i:s') }}
</div>
</body>
</html>
