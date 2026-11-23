## [2.0.4-kassa.2] - 2026-11-23 (fork EffegiCED)

### Fixed
- `eps-install.bat` controlla che PHP parta e, se manca il Visual C++ Redistributable di Microsoft (VCRUNTIME140.dll), lo scarica e lo installa: senza, il servizio `EPS-WS` partiva, si chiudeva subito e Windows lo metteva in pausa senza dire perché.
- Gli errori dei servizi finiscono in `logs\service-ws.log` e `logs\service-http.log`.

## [2.0.4-kassa.1] - 2026-11-23 (fork EffegiCED)

### Added
- Stampa grezza per Kassa: messaggi `raw` (byte ESC/POS in base64, verso `IP:porta` o una stampante di Windows) e `ping` (agente e stampante raggiungibili), con risposta JSON legata all'`id`.
- `config/agent.json` (`allowed_origins`) e `KASSA_ALLOWED_ORIGINS`: l'agente accetta stampe solo dai siti autorizzati; `eps-install.bat` chiede l'indirizzo di Kassa.

### Changed
- L'agente ascolta solo su `127.0.0.1` (prima `0.0.0.0`, aperto a tutta la rete); `--host` resta per chi lo vuole diverso.
- La pagina di configurazione accetta scritture solo da sé stessa e non scarica più `settings.html` da internet all'avvio.
- Il pacchetto è solo per Windows: niente più PHAR precompilato.

## [2.0.4] - 2025-10-31

- release: patch version 2.0.4 [Imam Ali Mustofa]
- refactor: stupid payload documentation and core server logic [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 2.0.3 [Imam Ali Mustofa]

## [2.0.3] - 2025-10-30

- release: patch version 2.0.3 [Imam Ali Mustofa]
- build: why my imagination is not working [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 2.0.2 [Imam Ali Mustofa]

## [2.0.2] - 2025-10-30

- release: patch version 2.0.2 [Imam Ali Mustofa]
- build: fix box build [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 2.0.1 [Imam Ali Mustofa]

## [2.0.1] - 2025-10-30

- release: patch version 2.0.1 [Imam Ali Mustofa]
- build: install box before create phar [Imam Ali Mustofa]
- chore: update CHANGELOG.md for major version 2.0.0 [Imam Ali Mustofa]

## [2.0.0] - 2025-10-30

- release: major version 2.0.0 [Imam Ali Mustofa]
- chore: add link [Imam Ali Mustofa]
- docs: update the docs and update bundle action [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.10 [Imam Ali Mustofa]

## [1.0.10] - 2025-10-30

- release: patch version 1.0.10 [Imam Ali Mustofa]
- chore: fix websocket client connection [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.9 [Imam Ali Mustofa]

## [1.0.9] - 2025-10-28

- release: patch version 1.0.9 [Imam Ali Mustofa]
- build: fix nested src and config directory [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.8 [Imam Ali Mustofa]

## [1.0.8] - 2025-10-28

- release: patch version 1.0.8 [Imam Ali Mustofa]
- build: new patch [Imam Ali Mustofa]
- build: try new github actions to create windows bundle [Imam Ali Mustofa]
- feat: split the ws and http server for Windows [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.6 [Imam Ali Mustofa]
- release: patch version 1.0.6 [Imam Ali Mustofa]
- feat: add web gui configuration [Imam Ali Mustofa]
- refactor: centeralized the lgos [Imam Ali Mustofa]
- chore: commit save point [Imam Ali Mustofa]
- chore: commit save point [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.5 [Imam Ali Mustofa]

## [1.0.6] - 2025-10-20

- release: patch version 1.0.6 [Imam Ali Mustofa]
- feat: add web gui configuration [Imam Ali Mustofa]
- refactor: centeralized the lgos [Imam Ali Mustofa]
- chore: commit save point [Imam Ali Mustofa]
- chore: commit save point [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.5 [Imam Ali Mustofa]

## [1.0.5] - 2025-10-11

- release: patch version 1.0.5 [Imam Ali Mustofa]
- chore: commit save point [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.4 [Imam Ali Mustofa]

## [1.0.4] - 2025-10-11

- release: patch version 1.0.4 [Imam Ali Mustofa]
- chore: commit save point [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.3 [Imam Ali Mustofa]

## [1.0.3] - 2025-10-11

- release: patch version 1.0.3 [Imam Ali Mustofa]
- chore: commit save point [Imam Ali Mustofa]
- chore: update CHANGELOG.md for patch version 1.0.2 [Imam Ali Mustofa]
