# Spelling Errors and Typos Found in Codebase

## Summary
Found 59+ problematic patterns with accented characters, misspellings, and inconsistent naming conventions across the codebase.

---

## 1. Database Column Names with Accents (Critical - Database Schema)

### `complèted_at` → Should be `completed_at`
- [database/migrations/2026_02_28_160100_create_missions_tables.php](database/migrations/2026_02_28_160100_create_missions_tables.php#L51)
  - Line 51: `$table->timestamp('complèted_at')->nullable()->index();` (user_missions table)
  - Line 55: `$table->index(['user_id', 'complèted_at'], 'user_missions_user_complèted_idx');` (index name)
  - Line 63: `$table->timestamp('complèted_at')->index();` (mission_completions table)
  - Line 67: `$table->index(['user_id', 'complèted_at'], 'mission_completions_user_complèted_idx');` (index name)

- [database/migrations/2026_03_15_170238_rename_completed_at_columns_remove_accents.php](database/migrations/2026_03_15_170238_rename_completed_at_columns_remove_accents.php#L16-L39)
  - Lines 16-39: Migration file references (only needed for documentation - already has migration)

### `notification_préférences` → Should be `notification_preferences`
- [database/migrations/2026_02_28_113455_create_notification_preferences_table.php](database/migrations/2026_02_28_113455_create_notification_preferences_table.php#L14)
  - Line 14: `Schema::create('notification_préférences', function (Blueprint $table) {`
  - Line 32: `Schema::dropIfExists('notification_préférences');`

---

## 2. Database Column Names in Matches Table (Critical - Database Schema)

### `compétition_*` → Should be `competition_*`
- [database/migrations/2026_03_08_140000_extend_matches_for_tournament_runs.php](database/migrations/2026_03_08_140000_extend_matches_for_tournament_runs.php#L14-L16)
  - Line 14: `$table->string('compétition_name', 160)->nullable()->after('event_name');`
  - Line 15: `$table->string('compétition_stage', 120)->nullable()->after('compétition_name');`
  - Line 16: `$table->string('compétition_split', 120)->nullable()->after('compétition_stage');`
  - Line 54-56: Drop index references in down() method

---

## 3. Model Properties with Accents

### [app/Models/EsportMatch.php](app/Models/EsportMatch.php#L43-L45)
- Line 43: `'compétition_name',` in $fillable array
- Line 44: `'compétition_stage',` in $fillable array
- Line 45: `'compétition_split',` in $fillable array
- Line 190: `return (string) ($this->event_name ?: $this->compétition_name ?: 'Tournoi Rocket League');`
- Line 203: `$this->compétition_name,`
- Line 204: `$this->compétition_split,`
- Line 205: `$this->compétition_stage,`

### [app/Models/AssistantFavorite.php](app/Models/AssistantFavorite.php#L15)
- Line 15: `'détails',` in $fillable array (should be 'details')
- Line 24: `'détails' => 'array',` in casts array

### [app/Models/ContactMessage.php](app/Models/ContactMessage.php#L110)
- Line 110: `return 'Non précise';` (French text in English codebase - should likely be 'Not Specified' or remove accent if kept)

---

## 4. Method Names with Accents (Critical - Code Structure)

### [app/Services/PlatformPointService.php](app/Services/PlatformPointService.php#L159)
- Line 159: `public function débit(` (method name with accent - should be `debit`)

### Usage of `débit()` method:
- [app/Application/Actions/Rewards/RedeemGiftAction.php](app/Application/Actions/Rewards/RedeemGiftAction.php#L83)
  - Line 83: `$walletResult = $this->platformPointService->débit(`
- [app/Application/Actions/Bets/PlaceBetAction.php](app/Application/Actions/Bets/PlaceBetAction.php#L116)
  - Line 116: `$this->platformPointService->débit(`
- [app/Services/ShopService.php](app/Services/ShopService.php#L54)
  - Line 54: `$walletResult = $this->platformPointService->débit(`
- [app/Services/Gifts/GiftCartService.php](app/Services/Gifts/GiftCartService.php#L355)
  - Line 355: `$walletResult = $this->platformPointService->débit(`

---

## 5. HTTP Request Validation Rules with Accents

### [app/Http/Requests/Api/Admin/StoreMatchRequest.php](app/Http/Requests/Api/Admin/StoreMatchRequest.php#L31-L33)
- Line 31: `'compétition_name' => ['nullable', 'string', 'max:160'],`
- Line 32: `'compétition_stage' => ['nullable', 'string', 'max:120'],`
- Line 33: `'compétition_split' => ['nullable', 'string', 'max:120'],`

---

## 6. Action Classes with Accented Variable Names

### [app/Application/Actions/Matches/CreateMatchAction.php](app/Application/Actions/Matches/CreateMatchAction.php#L46-L48)
- Lines 46-48: Multiple occurrences of `'compétition_name'`, `'compétition_stage'`, `'compétition_split'`

### [app/Application/Actions/Matches/UpdateMatchAction.php](app/Application/Actions/Matches/UpdateMatchAction.php#L56-L58)
- Lines 56-58: Multiple occurrences of `'compétition_name'`, `'compétition_stage'`, `'compétition_split'`

---

## 7. Seeder Files with Accents

### [database/seeders/PlatformPreviewSeeder.php](database/seeders/PlatformPreviewSeeder.php)
- Line 457: `'détails' => ['module' => 'gifts', 'priority' => 'high'],`
- Line 465: `'détails' => ['module' => 'gift_redemptions', 'priority' => 'medium'],`
- Line 473: `'détails' => ['module' => 'supporter', 'priority' => 'high'],`
- Line 498: `'détails' => (array) ($favorite['détails'] ?? []),` (appears twice)
- Line 1348: `$platformPoints->débit(`
- Line 1538-1540: `'compétition_name'`, `'compétition_stage'`, `'compétition_split'` in match data
- Line 1566-1568: Same as above
- Line 1594-1596: Same as above
- Line 1622: Same as above

---

## 8. Config Files with Accents and Misspellings

### [config/session.php](config/session.php)
- Line 12: `| This option déterminés the default session driver` (should be "determines")
- Line 141: `| The session cookie path déterminés the path` (should be "determines")
- Line 154: `| This value déterminés the domain` (should be "determines")
- Line 193: `| This option déterminés how your cookies behave` (should be "determines")

### [config/app.php](config/app.php)
- Line 23: `| This value déterminés the "environment"` (should be "determines")
- Line 75: `| The application locale déterminés the default locale` (should be "determines")

### [config/betting.php](config/betting.php)
- Line 53: `'Le résultat sportif est connu, mais les predictions ne sont pas encore reglees.'` (should be "réglées" not "reglees")
- Line 54: `'Le résultat a ete applique et les gains ont ete calcules.'` (should be "été" not "ete")

### [config/supporter.php](config/supporter.php)
- Line 91: `'Merci de soutenir ERAH. Reviens chaque mois pour récupérer ton bonus supporter.'` (French text in comments, acceptable but note accent usage)

### [config/assistant.php](config/assistant.php)
- Lines 56-66: Various French text entries with accents (content-related, acceptable)

---

## 9. Action Classes with French Text Issues

### [app/Application/Actions/Duels/ExpireDuelAction.php](app/Application/Actions/Duels/ExpireDuelAction.php#L85)
- Line 85: `message: 'Votre duel a expire sans réponse.',` (should be "expiré" not "expire")

### [app/Application/Actions/Clips/AddClipCommentAction.php](app/Application/Actions/Clips/AddClipCommentAction.php#L72)
- Line 72: `title: 'Nouvelle réponse',` (acceptable French text but different from other response notifications)

---

## 10. Service Classes with Accents

### [app/Services/GalleryPhotoImportService.php](app/Services/GalleryPhotoImportService.php#L19)
- Line 19: `'compétitions' => 'Competitions',` (mixing French key with English value)

### [app/Http/Controllers/Web/GiftPageController.php](app/Http/Controllers/Web/GiftPageController.php#L348)
- Line 348: `'Panier valide: '.$result['redemptions']->count().' commande(s) créée(s), '.$result['total_points'].' points débites.'`
  - Should be "débitées" not "débites" (gender/agreement issue)

### [app/Services/AdminOperationsCockpitService.php](app/Services/AdminOperationsCockpitService.php#L835)
- Line 835: `'label' => 'Credit/débit points',`
  - Mixing English "Credit" with French "débit" - should be either all in one language

---

## Priority Levels for Fixes

### 🔴 CRITICAL (Database/Schema Breaking)
1. `complèted_at` → `completed_at` (3 columns in migration, already has migration to fix)
2. `notification_préférences` → `notification_preferences` (table name)
3. `compétition_name/stage/split` → `competition_name/stage/split` (columns in matches table)

### 🟠 HIGH (Code Structure Breaking)
1. Method `débit()` → `debit()` and all 5 call sites
2. Model properties `'détails'` → `'details'` (2 occurrences in AssistantFavorite)

### 🟡 MEDIUM (Request Validation)
1. StoreMatchRequest validation rules with accents (3 fields)
2. CreateMatchAction & UpdateMatchAction with accented array keys (multiple lines)

### 🟢 LOW (Configuration/Comments)
1. Config file comments with French text and grammar issues
2. Seeder French text content
3. Action message French text with spelling errors

---

## Statistics
- **Total Accented Characters Found**: 59+
- **Files Affected**: 18+ PHP files
- **Database Schema Issues**: 3 (already tracked)
- **Method/Property Issues**: 6+
- **Config/Comment Issues**: 9+
- **Validation/Request Issues**: 3+

