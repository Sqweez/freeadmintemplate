# Client Export Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add an all-user XLSX download of every client matching the filters currently applied on the clients page.

**Architecture:** The Vue 2 page sends its current filter map, excluding pagination, to a new authenticated v3 endpoint. The endpoint reuses the same `ClientFilterDTO` and `ClientRepository` query as the paginated list, while a focused service writes the result to a styled PhpSpreadsheet workbook and streams it to the browser.

**Tech Stack:** PHP 8.0, Laravel 6, Eloquent, PHPUnit 8, PhpSpreadsheet 1.x, Vue 2, Axios, Vuetify.

---

## File map

- Modify `app/DTO/Filters/ClientFilterDTO.php`: parse nullable booleans correctly and add `without_code`.
- Modify `app/Repository/ClientRepository.php`: make every visible page filter effective in the shared query.
- Create `app/Services/Clients/ClientExportService.php`: build and stream the formatted workbook.
- Modify `app/Http/Controllers/api/v3/ClientController.php`: expose the download action.
- Modify `routes/api.php`: register `/api/v3/clients/export` before other client routes.
- Modify `resources/js/repositories/ClientRepository.js`: request the export as a blob.
- Modify `resources/js/views/v2/Clients/Clients.vue`: show the button to everyone, pass current filters, and download the response.
- Create `tests/Unit/DTO/Filters/ClientFilterDTOTest.php`: cover nullable boolean parsing.
- Create `tests/Unit/Repository/ClientRepositoryTest.php`: cover SQL constraints for all shared filters.
- Create `tests/Unit/Services/Clients/ClientExportServiceTest.php`: inspect workbook data and formatting.
- Create `tests/Feature/ClientExportTest.php`: verify route contract and pagination removal.

### Task 1: Make filter parsing explicit and nullable

**Files:**
- Create: `tests/Unit/DTO/Filters/ClientFilterDTOTest.php`
- Modify: `app/DTO/Filters/ClientFilterDTO.php`

- [ ] **Step 1: Write failing DTO tests**

Create tests asserting that absent `is_partner`, `is_wholesale_buyer`, `is_kaspi`, and `without_code` values are `null`, and that string/boolean `true` and `false` inputs remain distinguishable. Use a data provider for `['true', true, '1', 1]` and `['false', false, '0', 0]`.

- [ ] **Step 2: Verify RED**

Run `vendor/bin/phpunit tests/Unit/DTO/Filters/ClientFilterDTOTest.php`.

Expected: failures because absent flags currently become `false` and `without_code` does not exist.

- [ ] **Step 3: Implement nullable parsing**

Add `public ?bool` properties for the four boolean filters and parse them through one helper:

```php
private function nullableBoolean(array $payload, string $key): ?bool
{
    if (!array_key_exists($key, $payload)) {
        return null;
    }

    return filter_var($payload[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
}
```

Keep scalar filters unchanged and populate `without_code` in the constructor.

- [ ] **Step 4: Verify GREEN**

Run `vendor/bin/phpunit tests/Unit/DTO/Filters/ClientFilterDTOTest.php`.

Expected: all DTO tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/DTO/Filters/ClientFilterDTO.php tests/Unit/DTO/Filters/ClientFilterDTOTest.php
git commit -m "fix: parse client filters consistently"
```

### Task 2: Apply every page filter in the shared query

**Files:**
- Create: `tests/Unit/Repository/ClientRepositoryTest.php`
- Modify: `app/Repository/ClientRepository.php`

- [ ] **Step 1: Write failing repository tests**

For each payload below, build the repository query and assert its SQL/bindings contain the expected constraint:

```php
[
    ['search' => 'Alice'], ['%Alice%', 'Alice', '%Alice%'],
    ['loyalty_id' => 2], [2],
    ['gender' => 'F'], ['F'],
    ['client_city' => -1], [-1],
    ['is_partner' => 'false'], [false],
    ['is_partner' => 'true'], [true],
    ['is_wholesale_buyer' => 'true'], [true],
    ['is_kaspi' => 'true'], [true],
]
```

Add a separate assertion that `without_code=true` produces a grouped `client_card is null OR LENGTH(client_card) < 5` condition. Also assert absent optional flags do not add constraints for their columns.

- [ ] **Step 2: Verify RED**

Run `vendor/bin/phpunit tests/Unit/Repository/ClientRepositoryTest.php`.

Expected: failures for partner, wholesale, without-code, and absent Kaspi handling.

- [ ] **Step 3: Implement the shared constraints**

Enable `is_partner` and `is_wholesale_buyer` predicates, retain the existing search/loyalty/gender/city/Kaspi predicates, and add:

```php
->when($filters->without_code === true, function ($query) {
    return $query->where(function ($query) {
        return $query
            ->whereNull('client_card')
            ->orWhereRaw('LENGTH(client_card) < 5');
    });
})
```

Only apply checkbox filters when explicitly `true`; keep `is_partner=false` meaningful because it represents the «Клиент» option.

- [ ] **Step 4: Verify GREEN**

Run `vendor/bin/phpunit tests/Unit/Repository/ClientRepositoryTest.php`.

Expected: all repository tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Repository/ClientRepository.php tests/Unit/Repository/ClientRepositoryTest.php
git commit -m "fix: apply client list filters"
```

### Task 3: Generate a styled XLSX workbook

**Files:**
- Create: `tests/Unit/Services/Clients/ClientExportServiceTest.php`
- Create: `app/Services/Clients/ClientExportService.php`

- [ ] **Step 1: Write the failing service test**

Mock an Eloquent builder whose `chunkById(500, ...)` callback receives two `Client` models with loaded `city` relations. Capture the streamed response, load it through `PhpOffice\\PhpSpreadsheet\\IOFactory`, and assert:

```php
$this->assertSame('Клиенты', $sheet->getTitle());
$this->assertSame(
    ['Имя', 'Город', 'Сумма покупок за всё время', 'Телефон'],
    $sheet->rangeToArray('A1:D1')[0]
);
$this->assertSame('Анна', $sheet->getCell('A2')->getValue());
$this->assertSame('Алматы', $sheet->getCell('B2')->getValue());
$this->assertSame(125000, $sheet->getCell('C2')->getValue());
$this->assertSame('+77001234567', $sheet->getCell('D2')->getValue());
$this->assertSame(DataType::TYPE_STRING, $sheet->getCell('D2')->getDataType());
$this->assertSame('A1:D3', $sheet->getAutoFilter()->getRange());
$this->assertSame('A2', $sheet->getFreezePane());
```

Also assert that a client with the default city relation exports «Город не указан» and that the response filename ends in `.xlsx`.

- [ ] **Step 2: Verify RED**

Run `vendor/bin/phpunit tests/Unit/Services/Clients/ClientExportServiceTest.php`.

Expected: error because `ClientExportService` does not exist.

- [ ] **Step 3: Implement the export service**

Create a service with `download(Builder $query): StreamedResponse`. It must:

- create one sheet named `Клиенты`;
- write the four approved headings;
- call `select(['id', 'client_name', 'client_phone', 'client_city', 'cached_total_sale_amount'])` and `chunkById(500, ...)`;
- write amount cells numerically and phone cells via `setCellValueExplicit(..., DataType::TYPE_STRING)`;
- style the header, borders and alignment, freeze `A2`, set `A1:D{lastRow}` as the auto-filter range, use explicit readable widths, and apply `#,##0.00 [$₸-kk-KZ]` to column C;
- return `response()->streamDownload(...)` with the XLSX MIME type and a name like `clients_2026-09-05_14-30-00.xlsx`.

- [ ] **Step 4: Verify GREEN**

Run `vendor/bin/phpunit tests/Unit/Services/Clients/ClientExportServiceTest.php`.

Expected: the workbook test passes without warnings.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Clients/ClientExportService.php tests/Unit/Services/Clients/ClientExportServiceTest.php
git commit -m "feature: generate client excel export"
```

### Task 4: Add the authenticated export endpoint

**Files:**
- Create: `tests/Feature/ClientExportTest.php`
- Modify: `app/Http/Controllers/api/v3/ClientController.php`
- Modify: `routes/api.php`

- [ ] **Step 1: Write the failing endpoint test**

Mock `ClientRepository::query()` and `ClientExportService::download()`, disable only `AuthorizationMiddleware` and `ExceptionHandlingMiddleware`, request:

```text
GET /api/v3/clients/export?loyalty_id=2&page=9&per_page=50
```

Assert status 200, XLSX content type, attachment disposition, and that the DTO passed to the repository contains `loyalty_id=2`. Assert that the controller passes an unpaginated builder to the export service.

- [ ] **Step 2: Verify RED**

Run `vendor/bin/phpunit tests/Feature/ClientExportTest.php`.

Expected: 404 because the export route is absent.

- [ ] **Step 3: Implement controller and route**

Inject `ClientExportService` into an `export(Request $request, ClientExportService $service): StreamedResponse` action. Build the same repository query with `new ClientFilterDTO($request->all())`, clear unnecessary eager loads, add only `city`, and pass it to `download()` without calling `paginate()` or `get()`.

Register before `/search` and `/`:

```php
Route::get('/export', [\App\Http\Controllers\api\v3\ClientController::class, 'export']);
```

- [ ] **Step 4: Verify GREEN**

Run `vendor/bin/phpunit tests/Feature/ClientExportTest.php` and the three focused unit test files.

Expected: all focused backend tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/api/v3/ClientController.php routes/api.php tests/Feature/ClientExportTest.php
git commit -m "feature: expose client export endpoint"
```

### Task 5: Download the filtered export from the Vue page

**Files:**
- Modify: `resources/js/repositories/ClientRepository.js`
- Modify: `resources/js/views/v2/Clients/Clients.vue`

- [ ] **Step 1: Establish the frontend failure**

Run `rg -n "exportClients|exportingClients|/export" resources/js/repositories/ClientRepository.js resources/js/views/v2/Clients/Clients.vue`.

Expected: no active page export implementation is found.

- [ ] **Step 2: Add the repository method**

```js
async export(params = {}) {
    return axiosClient.get(`${baseURL}/export`, {
        params,
        responseType: 'blob',
    });
},
```

- [ ] **Step 3: Add the all-user button and download handler**

Replace the disabled modal trigger with a button that has no role condition:

```vue
<v-btn
    class="mt-2"
    color="success"
    :loading="exportingClients"
    :disabled="exportingClients"
    @click="exportClients"
>
    Экспорт клиентов
    <v-icon>mdi-file-excel-box</v-icon>
</v-btn>
```

Add `exportingClients: false` and an `exportClients()` method. Clone `Object.fromEntries(this.queryMap)`, delete `page` and `per_page`, request the blob, extract a safe filename from `content-disposition` with `clients.xlsx` as fallback, create a temporary object URL and anchor, click it, remove the anchor, revoke the URL, show a toast on failure, and reset the loading state in `finally`.

Also update `applyFilter()` so `client_city=0`, unchecked `without_code`, unchecked `is_kaspi`, and unchecked `is_wholesale_buyer` remove their query parameter. This keeps “all/unselected” states from accidentally constraining both list and export.

Remove the unused `ExportClientsModal` import, component registration, modal markup, and `exportModal` data field.

- [ ] **Step 4: Format and build**

Run:

```bash
npx prettier resources/js/repositories/ClientRepository.js resources/js/views/v2/Clients/Clients.vue --write
npm run dev
```

Expected: Laravel Mix completes successfully without Vue compilation errors.

- [ ] **Step 5: Commit**

```bash
git add resources/js/repositories/ClientRepository.js resources/js/views/v2/Clients/Clients.vue
git commit -m "feature: download filtered client export"
```

### Task 6: Final verification

**Files:**
- Verify all files changed in Tasks 1–5.

- [ ] **Step 1: Run focused backend tests**

```bash
vendor/bin/phpunit \
  tests/Unit/DTO/Filters/ClientFilterDTOTest.php \
  tests/Unit/Repository/ClientRepositoryTest.php \
  tests/Unit/Services/Clients/ClientExportServiceTest.php \
  tests/Feature/ClientExportTest.php
```

Expected: all tests pass.

- [ ] **Step 2: Run the complete backend suite**

Run `vendor/bin/phpunit`.

Expected: suite passes; if an unrelated pre-existing failure occurs, record the exact test and error separately.

- [ ] **Step 3: Run syntax and route checks**

```bash
php -l app/DTO/Filters/ClientFilterDTO.php
php -l app/Repository/ClientRepository.php
php -l app/Services/Clients/ClientExportService.php
php -l app/Http/Controllers/api/v3/ClientController.php
php artisan route:list --path=api/v3/clients
```

Expected: no syntax errors and both list/export routes appear.

- [ ] **Step 4: Rebuild frontend and inspect the diff**

Run `npm run dev`, `git diff --check`, and `git status --short`.

Expected: frontend build succeeds, no whitespace errors, and only intended source/test/documentation changes remain.

- [ ] **Step 5: Record production smoke checks**

After deployment, verify one unfiltered export and one export combining search, city, partner type, and a checkbox. Open both files in Excel and compare row counts/representative values with the page list.
