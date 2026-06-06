# Render Hosting Notes

Portfolio demo: Docker Web Service, SQLite, `render.yaml` Blueprint. **Setup steps are in [README.md](../README.md).**

## Repo files

| File | Role |
|------|------|
| `render.yaml` | Blueprint |
| `.env.render.example` | Env template |
| `Dockerfile` | Build (Composer + Vite) |
| `docker/render/start.sh` | Migrate, seed, serve |

## Demo logins

Demo users are created on **first seed only**. Passwords are **not** reset on subsequent deploys.

| Role | Email | Password |
|------|--------|----------|
| Admin | `admin@carpart.test` | Set `DEMO_ADMIN_PASSWORD` in Render Environment |
| Customer | `user@carpart.test` | Set `DEMO_USER_PASSWORD` (optional; falls back to admin password) |

**Security:** Use a strong, unique `DEMO_ADMIN_PASSWORD` in the Render dashboard. Do not commit it to git or publish it in public docs.

## Free tier

- Sleeps after ~15 min idle; cold start ~30–60s
- SQLite + seed on each container start when `APP_DEMO_MODE=true` (catalog reset; user passwords preserved after first create)
- Uploads on local disk may not survive redeploy

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 502 / build fail | Check logs; set `APP_KEY` and `APP_URL` |
| 500 everywhere | `APP_KEY=base64:…`, correct `APP_URL`, redeploy |
| 419 on login | Ensure `TRUSTED_PROXIES=*`, `SESSION_SECURE_COOKIE=true`, `SESSION_DRIVER=cookie`, clear cookies |
| No notifications | Set all `PUSHER_*` + `VITE_PUSHER_*`, redeploy (Vite bakes at build) |
| No products | Redeploy or `php artisan db:seed --class=RenderDemoSeeder --force` in Shell |

See also [UPSTASH-PUSHER.md](./UPSTASH-PUSHER.md) for Redis and Pusher setup.
