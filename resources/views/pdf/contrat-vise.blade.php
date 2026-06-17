<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #1a1a1a; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #166534; padding-bottom: 20px; margin-bottom: 25px; }
        .header img { height: 60px; }
        .header h1 { font-size: 18pt; font-weight: bold; color: #166534; margin: 10px 0 5px; }
        .header h2 { font-size: 13pt; color: #374151; margin: 0; }
        .visa-badge { background: #dcfce7; border: 2px solid #166534; border-radius: 8px; padding: 12px 20px; text-align: center; margin: 20px 0; }
        .visa-badge h3 { color: #166534; font-size: 14pt; margin: 0 0 5px; }
        .visa-badge p { color: #15803d; margin: 0; font-size: 10pt; }
        .section { margin-bottom: 20px; }
        .section h4 { font-size: 11pt; font-weight: bold; color: #1e3a5f; border-bottom: 1px solid #93c5fd; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 5px 8px; font-size: 10pt; }
        td:first-child { color: #6b7280; width: 40%; }
        td:last-child { font-weight: 600; color: #111827; }
        .footer { margin-top: 40px; border-top: 2px solid #166534; padding-top: 15px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 45%; }
        .signature-box .line { border-top: 1px solid #374151; margin-bottom: 8px; }
        .signature-box p { font-size: 9pt; color: #6b7280; margin: 0; }
        .watermark { color: #166534; font-size: 8pt; text-align: center; margin-top: 15px; }
        .numero { background: #f0fdf4; border: 1px solid #86efac; padding: 8px 15px; border-radius: 6px; font-family: monospace; font-size: 12pt; font-weight: bold; color: #166534; }
    </style>
</head>
<body>

<div class="header">
    <h1>RÉPUBLIQUE DU BÉNIN</h1>
    <p style="color:#6b7280;font-size:9pt;margin:2px 0">Ministère du Travail et de la Fonction Publique</p>
    <h2>DIRECTION GÉNÉRALE DU TRAVAIL</h2>
    <p style="font-size:10pt;color:#374151;margin-top:5px">Cotonou — Bénin</p>
</div>

<div style="text-align:center;margin-bottom:20px;">
    <div class="numero">N° {{ $dossier->numero_suivi }}</div>
</div>

<div class="visa-badge">
    <h3>✓ CONTRAT DE TRAVAIL VISÉ</h3>
    <p>Ce contrat a été examiné et visé conformément aux dispositions du Code du Travail béninois.</p>
    <p style="margin-top:5px;font-weight:600;">
    Date de visa : {{ $dossier->date_visa ? $dossier->date_visa->format('d/m/Y à H:i') : now()->format('d/m/Y à H:i') }}
    </p>
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
    <div class="signature-box">
        <br><br><br>
        <div class="line"></div>
        <p><strong>L'Employeur</strong></p>
        <p>{{ $dossier->nom_employeur }}</p>
    </div>
   <div class="signature-box">
        <br><br><br>
        <div class="line"></div>
        <p><strong>Le Directeur Général du Travail</strong></p>
        <p>ou son représentant habilité</p>
        <p style="margin-top:3px;">
            Visé le {{ $dossier->date_visa ? $dossier->date_visa->format('d/m/Y') : now()->format('d/m/Y') }}
        </p>
    </div>
</div>

<div class="watermark">
    Document officiel généré par la plateforme de dématérialisation de la DGT — Bénin<br>
    Référence : {{ $dossier->numero_suivi }} — {{ $dossier->date_visa ? $dossier->date_visa->format('d/m/Y à H:i:s') : now()->format('d/m/Y à H:i:s') }}
</div>

</body>
</html>
