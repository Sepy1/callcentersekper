Mahadata test instructions

1) Set your API key in `.env` (do not commit secrets):

MAHADATA_TOKEN=your_real_api_key_here

2) Example: send template (matches payload requested)

curl -X POST "https://messaging.mahadata.io/v1/116214948217846/messages" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "messaging_product": "whatsapp",
    "to": "6281234567890",
    "type": "template",
    "template": {
      "name": "nama_template",
      "language": { "code": "id" },
      "components": [
        { "type": "body", "parameters": [
            { "type": "text", "text": "param1" },
            { "type": "text", "text": "param2" },
            { "type": "text", "text": "param3" }
        ] }
      ]
    }
  }'

3) Example: send simple text

curl -X POST "https://messaging.mahadata.io/v1/116214948217846/messages" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "messaging_product": "whatsapp",
    "to": "6281234567890",
    "type": "text",
    "text": { "body": "Halo, ini pesan test." }
  }'

4) Quick test using the project's PHP script (after setting `.env` and clearing config cache):

php scripts/test_mahadata.php

Check `storage/logs/laravel.log` for request/response details.

Notes:
- The service class uses the `MAHADATA_TOKEN` from `config/services.php` (`services.mahadata.token`).
- Keep your API key secret. Do not commit `.env` with the real token.
