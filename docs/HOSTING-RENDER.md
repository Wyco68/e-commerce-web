# Render Hosting Notes

Portfolio demo: Docker Web Service, SQLite, `render.yaml` Blueprint. **Setup steps are in [README.md](../README.md).**

## Repo files

| File | Role |
|------|------|
| `render.yaml` | Blueprint |
| `.env.render.example` | Env template |
| `Dockerfile` | Build (Composer + Vite) |
| `docker/render/start.sh` | Migrate fresh, seed, serve |

## Demo logins

Same as [README.md](../README.md). On each deploy/restart with `APP_DEMO_MODE=true`, the database is reset and these accounts are recreated:

| Role | Email | Password |
|------|--------|----------|
| Admin | `admin@carpart.test` | `password` |
| Customer | `user@carpart.test` | `password` |

## Free tier

- Sleeps after ~15 min idle; cold start ~30–60s
- SQLite is wiped and re-seeded on every container start when `APP_DEMO_MODE=true`
- Uploads on local disk may not survive redeploy

## Manual reset (Render Shell)

```bash
php artisan migrate:fresh --force --seeder=RenderDemoSeeder
```

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 502 / build fail | Check logs; set `APP_KEY` and `APP_URL` |
| 500 everywhere | `APP_KEY=base64:…`, correct `APP_URL`, redeploy |
| 419 on login | Ensure `TRUSTED_PROXIES=*`, `SESSION_SECURE_COOKIE=true`, `SESSION_DRIVER=cookie`, clear cookies |
| No notifications | Set all `PUSHER_*` + `VITE_PUSHER_*`, redeploy (Vite bakes at build) |
| No products | Redeploy (demo mode runs `migrate:fresh --seeder=RenderDemoSeeder` on start) |
| Wrong login | Use README credentials above; redeploy to reset DB |

See also [UPSTASH-PUSHER.md](./UPSTASH-PUSHER.md) for Redis and Pusher setup.
