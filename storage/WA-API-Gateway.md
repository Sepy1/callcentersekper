# WhatsApp API Gateway (Call Center Sekper)

Overview
- **Purpose:** Proxy DEP (Indosat) WhatsApp API through the Laravel app so external clients (Postman, UI, automation) can send messages without embedding DEP credentials.
- **Location:** `app/Services/DepWhatsappService.php`, API routes under `routes/api.php` (`/api/wa/*`).

Authentication
- **Type:** Bearer token in `Authorization` header.
- **Header:** `Authorization: Bearer <token>`
- **Storage:** Tokens stored in `api_tokens` table (model `App\\Models\\ApiToken`).
- **Create token (quick):**
  - `php artisan tinker`
  - ```php
    use App\\Models\\ApiToken; use Illuminate\\Support\\Str;
    $token = Str::random(60);
    ApiToken::create(['name'=>'postman','token'=>$token]);
    echo $token;
    ```
- **Middleware:** `App\\Http\\Middleware\\EnsureApiBearerToken` verifies token and attaches `api_token` to request.

Endpoints
- Base path: `/api/wa`

- **GET /api/wa/templates**
  - Description: Returns list of approved WA templates retrieved from DEP (proxied and decrypted by server).
  - Auth: Required (Bearer token)
  - Response: JSON object, typically contains `data` array with objects having `template_id` and `template_name`.
  - Example (Tinker/Server output):
    ```json
    {
      "status":"SUCCESS",
      "data":[
        {"template_id":"1557389545282102","template_name":"ticket_created"},
        {"template_id":"734693812466588","template_name":"ticket_closed"}
      ]
    }
    ```

- **POST /api/wa/send**
  - Description: Send a WhatsApp message using server-side DEP credentials. Supports text and template sends.
  - Auth: Required (Bearer token)
  - Headers: `Content-Type: application/json`
  - Body parameters (JSON):
    - `phone` (string, required): recipient in international format (e.g., `6281234567890`).
    - `text` (string, optional): plain text message. If present and no template fields, sendText is used.
    - `template_id` (string, optional): template identifier (preferred if known).
    - `template_name` (string, optional): template name (alternative to id).
    - `language` (string, optional): language code (default `id`).
    - `params` (array, optional): array of template parameter strings.
  - Examples:
    - Send text:
      ```json
      {"phone":"6281234567890","text":"Halo, ini pesan otomatis"}
      ```
    - Send template by id:
      ```json
      {"phone":"6281234567890","template_id":"1557389545282102","language":"id","params":["Budi","TKT-0001"]}
      ```
    - Send template by name:
      ```json
      {"phone":"6281234567890","template_name":"ticket_created","language":"id","params":["Budi","TKT-0001"]}
      ```
  - Response: proxied DEP response (may be encrypted structure decrypted by `DepWhatsappService`). A successful acceptance often contains `message_id` and `phone_number` in `data`.

Server-side behavior
- `DepWhatsappService` builds encrypted request fields (`unique_id`, `iv`, `timestamp`, `param`, `auth_token`) and sets headers (`DEP-System-ID`, `DEP-Timestamp`, `DEP-Signature`).
- For templates, the service calls DEP op `broadcast` after extracting the `template_name` from template inquiry if `sendTemplateById` used.

Logging & Auditing
- API requests and responses are logged to DB table `api_request_logs` via middleware `App\\Http\\Middleware\\LogApiRequest`.
  - Key columns: `api_token_id`, `method`, `path`, `request_body`, `response_status`, `response_body`, `ip`, `created_at`.
- Application logs also contain `DEP REQUEST [op]` and `DEP RESPONSE [op]` entries in `storage/logs/laravel.log` for low-level debugging.

Troubleshooting Checklist (message accepted but not delivered)
1. Check `api_request_logs` latest entry for this request:
   - `php artisan tinker` then `App\\Models\\ApiRequestLog::latest()->first()->toArray()`
   - Inspect `response_body` — DEP acceptance vs errors.
2. Inspect `storage/logs/laravel.log` for `DEP REQUEST` and `DEP RESPONSE` blocks to see exact encrypted/decrypted payloads and HTTP status.
3. Verify `message_id` returned by DEP and ask DEP provider (or check DEP dashboard) for delivery status using that `message_id`.
4. Validate `template_id` / `template_name` and that template is approved in the WhatsApp account. Use `/api/wa/templates` to confirm available templates.
5. Check phone number format: must be international without `+`, e.g., `6285725681860`.
6. If DEP returns success but no delivery, contact DEP support with `message_id` and timestamps — delivery can be asynchronous and dependent on recipient device/network.

Postman & cURL Examples
- Postman: set header `Authorization: Bearer <token>` and `Content-Type: application/json`.
- cURL example (send template):
  ```bash
  curl -X POST "http://127.0.0.1:8000/api/wa/send" \\
    -H "Authorization: Bearer <token>" \\
    -H "Content-Type: application/json" \\
    -d '{"phone":"6281234567890","template_id":"1557389545282102","language":"id","params":["Budi","TKT-0001"]}'
  ```

Security & Recommendations
- Do not expose `DEP_SECRET_KEY` or `DEP_SALT` outside server env/config.
- Consider hashing stored API tokens and showing raw token only once on creation.
- Add rate-limiting or IP whitelisting for sensitive endpoints.
- Implement retention policy for `api_request_logs` (cleanup job) if table grows large.

IP restriction per token
- You can restrict which public IPs may use a given API token by setting `allowed_ips` on the token (stored as JSON array).
- Supported values:
  - Single IPv4 addresses, e.g. `203.0.113.5`
  - CIDR ranges (IPv4), e.g. `198.51.100.0/24`
- Example (create token limited to two IP ranges):
  ```php
  use App\Models\ApiToken; use Illuminate\Support\Str;
  $token = Str::random(60);
  ApiToken::create([
    'name' => 'postman',
    'token' => $token,
    'allowed_ips' => ['203.0.113.5','198.51.100.0/24']
  ]);
  echo $token;
  ```

If a token has `allowed_ips` set, requests presenting that token will be accepted only when the client IP (as seen by Laravel `Request::ip()`) matches one of the allowed entries; otherwise the gateway returns HTTP 403.

Maintenance
- If DEP changes encryption or op names, update `DepWhatsappService::buildEncryptedRequest()` accordingly.
- Keep templates in sync by periodically calling `/api/wa/templates` and validating `template_id` usage in application code.

Contact & Support
- For DEP-related delivery inquiries, provide DEP `message_id`, timestamps, and raw DEP REQUEST/RESPONSE logs to DEP support.

Document History
- Created: 2026-01-14
