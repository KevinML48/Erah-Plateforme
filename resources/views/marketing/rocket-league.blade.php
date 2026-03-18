@php
    $templatePath = base_path('_template_site/valorant-VCL.html');
    $templateHtml = is_file($templatePath) ? file_get_contents($templatePath) : false;

    if ($templateHtml === false) {
        throw new RuntimeException('Unable to load the Valorant VCL template.');
    }

    $templateHtml = strtr($templateHtml, [
        'href="assets/' => 'href="/template/assets/',
        'src="assets/' => 'src="/template/assets/',
        'href="/assets/' => 'href="/template/assets/',
        'src="/assets/' => 'src="/template/assets/',
        'href="index.html"' => 'href="/"',
        'href="about.html"' => 'href="/about"',
        'href="boutique.html"' => 'href="/boutique"',
        'href="valorant-VCL.html"' => 'href="/rocket-league"',
        'href="staff.html"' => 'href="/staff"',
        'href="medical.html"' => 'href="/medical"',
        'href="galerie-photos.html"' => 'href="/galerie-photos"',
        'href="galerie-video.html"' => 'href="/galerie-video"',
        'href="evenement.html"' => 'href="/evenement"',
        'href="nos-stages.html"' => 'href="/nos-stages"',
        'href="mende.html"' => 'href="/mende"',
        'href="contact.html"' => 'href="/contact"',
        'href="mentions-legales.html"' => 'href="/mentions-legales"',
    ]);

    libxml_use_internal_errors(true);

    $dom = new DOMDocument();
    $dom->loadHTML('<' . '?xml encoding="UTF-8"?>' . $templateHtml);

    $xpath = new DOMXPath($dom);

    $setTextAtIndex = static function (DOMXPath $xpath, string $query, int $index, string $text): void {
        $nodes = $xpath->query($query);

        if ($nodes instanceof DOMNodeList && $nodes->item($index) instanceof DOMNode) {
            $nodes->item($index)->textContent = $text;
        }
    };

    $setAttributeAtIndex = static function (DOMXPath $xpath, string $query, int $index, string $attribute, string $value): void {
        $nodes = $xpath->query($query);

        if ($nodes instanceof DOMNodeList && $nodes->item($index) instanceof DOMElement) {
            $nodes->item($index)->setAttribute($attribute, $value);
        }
    };

    $setTextAtIndex($xpath, '//title', 0, 'Rocket League | ERAH Esport');
    $setAttributeAtIndex($xpath, '//meta[@name="description"]', 0, 'content', 'Découvrez notre équipe Rocket League avec ERAH Esport : joueurs, staff et ambitions compétitives.');
    $setAttributeAtIndex($xpath, '//meta[@name="keywords"]', 0, 'content', 'Rocket League, ERAH Esport, equipe Rocket League, roster Rocket League, joueurs Rocket League, staff Rocket League, esport Lozere');
    $setTextAtIndex($xpath, '//h2[contains(@class, "ph-caption-subtitle")]', 0, 'Présentation officielle');
    $setTextAtIndex($xpath, '//h1[contains(@class, "ph-caption-title")]', 0, 'Notre équipe Rocket League');
    $setTextAtIndex($xpath, '//div[contains(@class, "ph-caption-description")]', 0, 'Nous sommes fiers de vous présenter notre équipe Rocket League. Une formation engagée qui portera les couleurs d ERah Esport en compétition.');
    $setTextAtIndex($xpath, '//h2[contains(@class, "ph-caption-subtitle")]', 1, 'Fiers de notre roster');
    $setTextAtIndex($xpath, '//h1[contains(@class, "ph-caption-title")]', 1, 'Joueurs et staff');
    $setTextAtIndex($xpath, '//div[contains(@class, "ph-caption-description")]', 1, 'Découvrez les joueurs et le staff qui accompagnent le projet Rocket League d ERAH Esport sur la saison en cours.');
    $setTextAtIndex($xpath, '//*[local-name()="textPath"]', 0, 'Fiers de notre équipe Rocket League - ERAH Esport');
    $setTextAtIndex($xpath, '//a[@href="/rocket-league"]', 0, 'Rocket League');

    $setAttributeAtIndex($xpath, '//a[contains(@class, "tt-link") and normalize-space()="Page VLR"]', 0, 'href', '/rocket-league');
    $setTextAtIndex($xpath, '//a[contains(@class, "tt-link") and normalize-space()="Page VLR"]', 0, 'Equipe Rocket League');
    $setAttributeAtIndex($xpath, '//a[contains(@class, "tt-link") and normalize-space()="Ligue Invite"]', 0, 'href', 'https://www.twitch.tv/erah_association');
    $setTextAtIndex($xpath, '//a[contains(@class, "tt-link") and normalize-space()="Ligue Invite"]', 0, 'Twitch ERAH');

    $profiles = [
        ['name' => '17Saizen', 'category' => 'Rocket League', 'role' => 'Joueur', 'href' => 'https://x.com/17saizen', 'image' => '/template/assets/img/rocket-league/saizen.jpg'],
        ['name' => 'AnoriQK', 'category' => 'Rocket League', 'role' => 'Joueur', 'href' => 'https://x.com/AnoriQK', 'image' => '/template/assets/img/rocket-league/anoriq.jpg'],
        ['name' => 'MayKooRL', 'category' => 'Rocket League', 'role' => 'Joueur', 'href' => 'https://x.com/MayKooRL', 'image' => '/template/assets/img/rocket-league/mayko.jpg'],
        ['name' => 'BeastBound', 'category' => 'Coach', 'role' => 'Staff', 'href' => 'https://x.com/BeastBoundLive', 'image' => '/template/assets/img/rocket-league/BeastBound.jpg'],
        ['name' => 'Zhin', 'category' => 'Manager', 'role' => 'Staff', 'href' => 'https://x.com/Zhin_rl', 'image' => '/template/assets/img/rocket-league/zhin.jpg'],
    ];

    $profileCards = $xpath->query('//div[contains(@class, "tt-ppl-items-list")]/a[contains(@class, "tt-ppl-item")]');

    if ($profileCards instanceof DOMNodeList) {
        for ($index = $profileCards->length - 1; $index >= count($profiles); $index--) {
            $card = $profileCards->item($index);

            if ($card instanceof DOMNode && $card->parentNode instanceof DOMNode) {
                $card->parentNode->removeChild($card);
            }
        }

        for ($index = 0; $index < count($profiles); $index++) {
            $card = $profileCards->item($index);

            if (! $card instanceof DOMElement) {
                continue;
            }

            $profile = $profiles[$index];
            $card->setAttribute('href', $profile['href']);

            $image = $xpath->query('.//img', $card)->item(0);
            if ($image instanceof DOMElement) {
                $image->setAttribute('src', $profile['image']);
                $image->setAttribute('alt', $profile['name']);
            }

            $title = $xpath->query('.//h2[contains(@class, "tt-ppli-title")]', $card)->item(0);
            if ($title instanceof DOMNode) {
                $title->textContent = $profile['name'];
            }

            $category = $xpath->query('.//div[contains(@class, "tt-ppli-category")]', $card)->item(0);
            if ($category instanceof DOMNode) {
                $category->textContent = $profile['category'];
            }

            $role = $xpath->query('.//div[contains(@class, "tt-ppli-info")]', $card)->item(0);
            if ($role instanceof DOMNode) {
                $role->textContent = $profile['role'];
            }
        }
    }

    $renderedHtml = $dom->saveHTML();
    $xmlDeclaration = '<' . '?xml encoding="UTF-8"?' . '>';
    $xmlDeclarationWithoutClosing = '<' . '?xml encoding="UTF-8"' . '>';
    $renderedHtml = str_replace([$xmlDeclaration, $xmlDeclarationWithoutClosing], '', $renderedHtml);

    echo $renderedHtml;
@endphp