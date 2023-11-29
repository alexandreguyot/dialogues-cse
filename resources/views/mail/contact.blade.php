<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <h2>Prise de contact sur mon site</h2>
    <p>Réception d'une prise de contact avec les éléments suivants :</p>
    <ul>
      <li><strong>Nom</strong> : {{ $contact['lastname'] }}</li>
      <li><strong>Prénom</strong> : {{ $contact['firstname'] }}</li>
      <li><strong>Téléphone</strong> : {{ $contact['telephone'] }}</li>
      <li><strong>Email</strong> : {{ $contact['email'] }}</li>
      <li><strong>Société</strong> : {{ $contact['societe'] }}</li>
      <li><strong>Message</strong> : {{ $contact['message'] }}</li>
    </ul>
  </body>
</html>
