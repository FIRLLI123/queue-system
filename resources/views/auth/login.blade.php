<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - Queue Dashboard CEC</title>

    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary:#2f53c3;
            --primary-light:#5d7de0;
            --primary-dark:#1f3c93;
            --primary-pale:#eaeffb;
            --blob-soft:#8aa6f2;
            --hue-violet:#8a6ff0;
            --hue-pink:#ec5fa3;
            --hue-cyan:#27d3c9;
            --bg-page:#eef1fa;
            --surface:#ffffff;
            --border:#e3e7f5;
            --text-primary:#1a2340;
            --text-muted:#6b7494;
            --text-faint:#a0a8c4;
            --live:#16a34a;
            --danger:#dc4a52;
            --radius-xl:30px;
            --radius-lg:20px;
            --radius-md:13px;
            --font-display:'Space Grotesk',sans-serif;
            --font-body:'Inter',sans-serif;
            --font-mono:'JetBrains Mono',monospace;
        }
        *{box-sizing:border-box;}
        html,body{height:100%;}
        body{
            margin:0;
            font-family:var(--font-body);
            color:var(--text-primary);
            background:radial-gradient(ellipse at top,#f4f7ff 0%,#eef1fa 55%,#e6ecfb 100%);
            min-height:100vh;
            overflow-x:hidden;
            position:relative;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:36px 20px;
        }

        /* soft drifting dot texture across the whole page */
        .bg-grid{
            position:fixed;inset:0;z-index:0;pointer-events:none;
            background-image:radial-gradient(rgba(47,83,195,.16) 1.4px,transparent 1.4px);
            background-size:30px 30px;
            opacity:.5;
            animation:gridDriftPage 34s linear infinite;
        }
        @keyframes gridDriftPage{0%{background-position:0 0;}100%{background-position:60px 60px;}}

        /* slow rotating aurora wash for gentle colour movement */
        .aurora{
            position:fixed;top:50%;left:50%;z-index:0;pointer-events:none;
            width:1100px;height:1100px;
            transform:translate(-50%,-50%);
            background:conic-gradient(from 0deg,var(--primary-light),var(--hue-violet),var(--hue-pink),var(--hue-cyan),var(--primary),var(--primary-light));
            filter:blur(150px);
            opacity:.3;
            animation:auroraSpin 70s linear infinite;
        }
        @keyframes auroraSpin{to{transform:translate(-50%,-50%) rotate(360deg);}}

        .blob{position:fixed;border-radius:50%;filter:blur(70px);pointer-events:none;z-index:0;}
        .blob-a{width:480px;height:480px;top:-140px;left:-120px;opacity:.45;background:radial-gradient(circle,var(--primary-light),transparent 70%);animation:floatA 16s ease-in-out infinite;}
        .blob-b{width:420px;height:420px;bottom:-160px;right:-100px;opacity:.42;background:radial-gradient(circle,var(--hue-pink),transparent 70%);animation:floatB 19s ease-in-out infinite;}
        .blob-c{width:320px;height:320px;top:10%;right:8%;opacity:.4;background:radial-gradient(circle,var(--hue-cyan),transparent 70%);animation:floatC 21s ease-in-out infinite;}
        .blob-d{width:300px;height:300px;bottom:10%;left:5%;opacity:.4;background:radial-gradient(circle,var(--hue-violet),transparent 70%);animation:floatD 24s ease-in-out infinite;}
        @keyframes floatA{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(40px,30px) scale(1.08);}}
        @keyframes floatB{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(-30px,-40px) scale(1.05);}}
        @keyframes floatC{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(-26px,32px) scale(1.07);}}
        @keyframes floatD{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(30px,-22px) scale(1.05);}}

        /* small particles drifting upward through the page */
        .particle{position:fixed;z-index:0;pointer-events:none;border-radius:50%;opacity:0;animation:floatParticle linear infinite;}
        @keyframes floatParticle{
            0%{transform:translateY(0) translateX(0);opacity:0;}
            12%{opacity:.55;}
            88%{opacity:.4;}
            100%{transform:translateY(-118vh) translateX(24px);opacity:0;}
        }

        .shell{
            position:relative;z-index:1;
            width:100%;max-width:980px;
            background:var(--surface);
            border-radius:var(--radius-xl);
            overflow:hidden;
            box-shadow:0 40px 90px -35px rgba(47,83,195,.35),0 1px 0 rgba(255,255,255,.6) inset;
            display:grid;
            grid-template-columns:1fr 1.08fr;
            opacity:0;
            animation:shellIn .6s cubic-bezier(.2,.8,.2,1) forwards;
        }
        @keyframes shellIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}

        /* ---------- LEFT: brand / live monitor panel ---------- */
        .monitor{
            position:relative;
            overflow:hidden;
            background:linear-gradient(155deg,var(--primary),var(--primary-dark) 120%);
            padding:46px 42px;
            display:flex;flex-direction:column;justify-content:space-between;
            color:#fff;
        }
        .monitor::before{
            content:"";position:absolute;inset:0;
            background-image:radial-gradient(rgba(255,255,255,.16) 1.4px,transparent 1.4px);
            background-size:24px 24px;
            mask-image:radial-gradient(ellipse at 25% 20%,black 0%,transparent 65%);
            animation:dotsDrift 26s linear infinite;
            pointer-events:none;
        }
        @keyframes dotsDrift{
            0%{background-position:0 0;}
            100%{background-position:48px 24px;}
        }
        .ring-deco{
            position:absolute;width:230px;height:230px;border-radius:50%;
            border:1px solid rgba(255,255,255,.18);
            top:-60px;right:-60px;
            animation:ringSpin 40s linear infinite;
        }
        .ring-deco::after{
            content:"";position:absolute;inset:26px;border-radius:50%;
            border:1px solid rgba(255,255,255,.12);
        }
        @keyframes ringSpin{to{transform:rotate(360deg);}}

        .brand{position:relative;display:flex;align-items:center;gap:13px;}
        .brand-mark{
            width:42px;height:42px;border-radius:13px;
            background:rgba(255,255,255,.16);
            border:1px solid rgba(255,255,255,.3);
            display:flex;align-items:center;justify-content:center;
            font-size:17px;color:#fff;
        }
        .brand-text h1{font-family:var(--font-display);font-size:18px;font-weight:600;margin:0;}
        .brand-text p{margin:2px 0 0;font-size:11.5px;color:rgba(255,255,255,.65);font-family:var(--font-mono);letter-spacing:.4px;}

        .live-badge{
            position:relative;display:inline-flex;align-items:center;gap:8px;
            margin-top:30px;width:fit-content;padding:6px 14px 6px 10px;
            border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.12);
            border-radius:99px;font-family:var(--font-mono);font-size:11px;
            letter-spacing:.5px;color:#fff;text-transform:uppercase;
        }
        .live-dot{width:7px;height:7px;border-radius:50%;background:#5be08a;animation:pulseDot 1.8s ease-out infinite;}
        @keyframes pulseDot{
            0%{box-shadow:0 0 0 0 rgba(91,224,138,.6);}
            70%{box-shadow:0 0 0 9px rgba(91,224,138,0);}
            100%{box-shadow:0 0 0 0 rgba(91,224,138,0);}
        }

        .headline{
            position:relative;font-family:var(--font-display);font-weight:600;
            font-size:clamp(22px,2.3vw,28px);line-height:1.3;margin:20px 0 0;max-width:380px;
        }
        .headline span{color:#bdd0ff;}
        .sub{position:relative;color:rgba(255,255,255,.78);font-size:14px;line-height:1.6;max-width:360px;margin:10px 0 0;}

        .console{
            position:relative;margin-top:26px;border:1px solid rgba(255,255,255,.2);
            background:rgba(255,255,255,.1);border-radius:var(--radius-md);
            padding:15px 17px;backdrop-filter:blur(8px);
        }
        .console-head{display:flex;justify-content:space-between;align-items:center;font-family:var(--font-mono);font-size:10.5px;letter-spacing:.5px;color:rgba(255,255,255,.6);text-transform:uppercase;margin-bottom:11px;}
        .console-head .dots span{display:inline-block;width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.5);margin-left:4px;}
        .ticker{height:138px;overflow:hidden;position:relative;}
        .ticker-row{
            display:flex;align-items:center;gap:9px;font-family:var(--font-mono);font-size:12px;
            padding:7px 0;border-bottom:1px dashed rgba(255,255,255,.16);
            opacity:0;transform:translateY(8px);animation:rowIn .5s ease forwards;
        }
        .ticker-row:last-child{border-bottom:none;}
        @keyframes rowIn{to{opacity:1;transform:translateY(0);}}
        .ticker-row .code{background:rgba(255,255,255,.92);color:var(--primary-dark);font-weight:600;min-width:50px;padding:2px 7px;border-radius:6px;text-align:center;font-size:11px;}
        .ticker-row .where{color:#fff;flex:1;}
        .ticker-row .stamp{color:rgba(255,255,255,.55);font-size:10.5px;}

        .metrics{position:relative;display:flex;gap:13px;margin-top:24px;}
        .metric{flex:1;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);border-radius:var(--radius-md);padding:12px 15px;}
        .metric p{margin:0;}
        .metric .label{font-size:10.5px;color:rgba(255,255,255,.6);font-family:var(--font-mono);letter-spacing:.4px;text-transform:uppercase;}
        .metric .value{font-family:var(--font-display);font-size:20px;font-weight:600;margin-top:3px;color:#fff;}

        /* ---------- RIGHT: auth form panel ---------- */
        .auth-wrap{
            position:relative;overflow:hidden;
            background:linear-gradient(165deg,#ffffff 0%,#f5f8ff 100%);
            padding:50px 46px;display:flex;flex-direction:column;justify-content:center;
        }
        .auth-wrap::before{
            content:"";position:absolute;z-index:-1;
            width:280px;height:280px;border-radius:50%;right:-100px;bottom:-110px;
            background:radial-gradient(circle,rgba(236,95,163,.16),transparent 70%);
            animation:floatC 18s ease-in-out infinite;
        }
        .auth-wrap::after{
            content:"";position:absolute;z-index:-1;
            width:180px;height:180px;border-radius:50%;left:-70px;top:-70px;
            background:radial-gradient(circle,rgba(138,111,240,.14),transparent 70%);
            animation:floatD 22s ease-in-out infinite;
        }

        .card-item{opacity:0;transform:translateY(10px);animation:itemIn .55s ease forwards;}
        @keyframes itemIn{to{opacity:1;transform:translateY(0);}}
        .card-item.d1{animation-delay:.18s;}
        .card-item.d2{animation-delay:.26s;}
        .card-item.d3{animation-delay:.34s;}
        .card-item.d4{animation-delay:.42s;}
        .card-item.d5{animation-delay:.5s;}
        .card-item.d6{animation-delay:.58s;}

        .eyebrow{font-family:var(--font-mono);font-size:11px;letter-spacing:1.5px;color:var(--primary);text-transform:uppercase;margin:0;}
        .auth-wrap h2{font-family:var(--font-display);font-weight:600;font-size:25px;margin:8px 0 4px;color:var(--text-primary);}
        .auth-wrap .lead{color:var(--text-muted);font-size:13.5px;margin:0 0 26px;}

        form{margin:0;}
        .field{position:relative;margin-bottom:18px;}
        .field i.field-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--text-faint);transition:color .2s;}
        .field input{
            width:100%;background:var(--surface);border:1.4px solid var(--border);
            border-radius:var(--radius-md);padding:15px 14px 15px 40px;
            font-family:var(--font-body);font-size:14px;color:var(--text-primary);
            outline:none;transition:border-color .2s,box-shadow .2s;
        }
        .field input::placeholder{color:transparent;}
        .field label{
            position:absolute;left:40px;top:50%;transform:translateY(-50%);
            font-size:14px;color:var(--text-faint);pointer-events:none;
            transition:all .18s ease;background:transparent;padding:0 4px;
        }
        .field input:focus,.field input:not(:placeholder-shown){
            border-color:var(--primary);box-shadow:0 0 0 4px var(--primary-pale);
        }
        .field input:focus + label,.field input:not(:placeholder-shown) + label{
            top:0;left:34px;font-size:11px;background:#fff;color:var(--primary);letter-spacing:.3px;
        }
        .field input:focus ~ i.field-icon{color:var(--primary);}

        .toggle-pass{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-faint);cursor:pointer;font-size:13.5px;padding:6px;line-height:1;}
        .toggle-pass:hover{color:var(--primary);}

        .field--error input{border-color:var(--danger);box-shadow:0 0 0 4px rgba(220,74,82,.12);}
        .field-error-msg{display:flex;align-items:center;gap:6px;color:var(--danger);font-size:12px;margin:-10px 0 16px 4px;animation:shake .35s ease;}
        @keyframes shake{0%,100%{transform:translateX(0);}25%{transform:translateX(-4px);}75%{transform:translateX(4px);}}

        .row-between{display:flex;align-items:center;justify-content:space-between;margin:2px 0 22px;}
        .remember{display:flex;align-items:center;gap:9px;font-size:13px;color:var(--text-muted);}
        .remember input[type="checkbox"]{
            appearance:none;width:17px;height:17px;border-radius:5px;
            border:1.4px solid var(--border);background:#fff;
            display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;
        }
        .remember input[type="checkbox"]:checked{background:var(--primary);border-color:var(--primary);}
        .remember input[type="checkbox"]:checked::after{content:"\f00c";font-family:"Font Awesome 5 Free";font-weight:900;font-size:9.5px;color:#fff;}

        .btn-submit{
            position:relative;width:100%;border:none;border-radius:var(--radius-md);
            padding:15px;font-family:var(--font-body);font-weight:600;font-size:14.5px;
            color:#fff;cursor:pointer;overflow:hidden;
            background:linear-gradient(95deg,var(--primary),var(--primary-dark));
            display:flex;align-items:center;justify-content:center;gap:8px;
            transition:transform .15s ease,box-shadow .15s ease;
            box-shadow:0 14px 28px -12px rgba(47,83,195,.55);
        }
        .btn-submit:hover{transform:translateY(-1px);box-shadow:0 18px 34px -12px rgba(47,83,195,.7);}
        .btn-submit:active{transform:translateY(0);}
        .btn-submit::before{
            content:"";position:absolute;top:0;left:-60%;width:40%;height:100%;
            background:linear-gradient(120deg,transparent,rgba(255,255,255,.45),transparent);
            transform:skewX(-20deg);animation:sweep 3.2s ease-in-out infinite;
        }
        @keyframes sweep{0%{left:-60%;}45%{left:120%;}100%{left:120%;}}
        .btn-submit .spinner{width:15px;height:15px;border-radius:50%;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;display:none;animation:spin .7s linear infinite;}
        .btn-submit.is-loading .spinner{display:inline-block;}
        .btn-submit.is-loading .btn-label{display:none;}
        @keyframes spin{to{transform:rotate(360deg);}}

        /* thin water-ripple hover accent */
        .ripple-ring{
            position:absolute;left:50%;top:50%;
            width:10px;height:10px;border-radius:50%;
            transform:translate(-50%,-50%) scale(0);
            pointer-events:none;visibility:hidden;
            animation:rippleOut 1.6s ease-out infinite;
        }
        .btn-submit .ripple-ring{border:1.5px solid rgba(255,255,255,.65);}
        .toggle-pass .ripple-ring{border:1.5px solid rgba(47,83,195,.55);width:8px;height:8px;}
        .btn-submit:hover .ripple-ring,
        .toggle-pass:hover .ripple-ring{visibility:visible;}
        .ripple-ring.r2{animation-delay:.7s;}
        @keyframes rippleOut{
            0%{transform:translate(-50%,-50%) scale(0);opacity:.4;}
            70%{opacity:.12;}
            100%{transform:translate(-50%,-50%) scale(6);opacity:0;}
        }

        .divider{border:none;border-top:1px solid var(--border);margin:24px 0 18px;}
        .seed-note{text-align:center;font-size:12px;color:var(--text-faint);}
        .seed-note code{background:var(--primary-pale);border:1px solid var(--border);padding:2px 7px;border-radius:6px;font-family:var(--font-mono);color:var(--primary);font-size:11.5px;}

        @media (max-width:860px){
            body{padding:0;}
            .shell{grid-template-columns:1fr;border-radius:0;max-width:100%;min-height:100vh;}
            .monitor{padding:34px 30px;}
            .monitor .console,.monitor .metrics{display:none;}
            .headline{max-width:100%;}
            .auth-wrap{padding:36px 28px 44px;}
        }

        @media (prefers-reduced-motion:reduce){
            *{animation-duration:.001ms !important;animation-iteration-count:1 !important;transition:none !important;}
            .particle,.aurora{display:none;}
        }
    </style>
</head>
<body>
    <div class="bg-grid" aria-hidden="true"></div>
    <div class="aurora" aria-hidden="true"></div>
    <div class="blob blob-a" aria-hidden="true"></div>
    <div class="blob blob-b" aria-hidden="true"></div>
    <div class="blob blob-c" aria-hidden="true"></div>
    <div class="blob blob-d" aria-hidden="true"></div>

    <div class="particle" aria-hidden="true" style="left:8%;width:7px;height:7px;background:var(--primary-light);animation-duration:17s;animation-delay:0s;"></div>
    <div class="particle" aria-hidden="true" style="left:18%;width:5px;height:5px;background:var(--hue-cyan);animation-duration:14s;animation-delay:3s;"></div>
    <div class="particle" aria-hidden="true" style="left:32%;width:9px;height:9px;background:var(--hue-pink);animation-duration:21s;animation-delay:1.5s;"></div>
    <div class="particle" aria-hidden="true" style="right:26%;width:6px;height:6px;background:var(--hue-violet);animation-duration:19s;animation-delay:5s;"></div>
    <div class="particle" aria-hidden="true" style="right:14%;width:8px;height:8px;background:var(--hue-cyan);animation-duration:16s;animation-delay:2.2s;"></div>
    <div class="particle" aria-hidden="true" style="right:6%;width:5px;height:5px;background:var(--hue-pink);animation-duration:23s;animation-delay:4.3s;"></div>

    <div class="shell">

        <!-- LEFT: brand / live queue monitor -->
        <div class="monitor">
            <div class="ring-deco"></div>
            <div>
                <div class="brand">
                    <div class="brand-mark"><i class="fas fa-layer-group" aria-hidden="true"></i></div>
                    <div class="brand-text">
                        <h1>Queue Dashboard CEC</h1>
                        <p>INTERNAL MONITORING SYSTEM</p>
                    </div>
                </div>

                <div class="live-badge"><span class="live-dot"></span> Live &middot; pemantauan real-time</div>

                <h2 class="headline"><span>Pantau semuanya dari satu layar.</span></h2>
                <p class="sub">Masuk untuk melihat status antrian, durasi tunggu, dan aktivitas panggilan secara langsung.</p>

                <div class="console">
                    <div class="console-head">
                        <span>Antrian terbaru</span>
                        <span class="dots"><span></span><span></span><span></span></span>
                    </div>
                    <div class="ticker" id="ticker"></div>
                </div>
            </div>

            <div class="metrics">
                <div class="metric">
                    <p class="label">Antrian aktif</p>
                    <p class="value" id="metricActive">0</p>
                </div>
                <div class="metric">
                    <p class="label">Rata-rata tunggu</p>
                    <p class="value" id="metricWait">0.0m</p>
                </div>
            </div>
        </div>

        <!-- RIGHT: login form -->
        <div class="auth-wrap">
            <p class="eyebrow card-item">Auth</p>
            <h2 class="card-item d1">Selamat datang kembali</h2>
            <p class="lead card-item d1">Masuk untuk mengakses dashboard antrian.</p>

            <form class="user" method="POST" action="{{ route('login.attempt') }}" id="loginForm">
                @csrf

                <div class="field card-item d2 @error('username') field--error @enderror">
                    <i class="fas fa-user field-icon" aria-hidden="true"></i>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder=" "
                        required
                        autofocus
                    >
                    <label for="username">User ID</label>
                </div>
                @error('username')
                    <p class="field-error-msg"><i class="fas fa-circle-exclamation" aria-hidden="true"></i> {{ $message }}</p>
                @enderror

                <div class="field card-item d3 @error('password') field--error @enderror">
                    <i class="fas fa-lock field-icon" aria-hidden="true"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder=" "
                        required
                    >
                    <label for="password">Password</label>
                    <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan password">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                        <span class="ripple-ring" aria-hidden="true"></span>
                    </button>
                </div>
                @error('password')
                    <p class="field-error-msg"><i class="fas fa-circle-exclamation" aria-hidden="true"></i> {{ $message }}</p>
                @enderror

                <div class="row-between card-item d4">
                    <label class="remember">
                        <input type="checkbox" name="remember" id="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn-submit card-item d5">
                    <span class="spinner"></span>
                    <span class="btn-label">Masuk</span>
                    <i class="fas fa-arrow-right btn-label" aria-hidden="true"></i>
                    <span class="ripple-ring r1" aria-hidden="true"></span>
                    <span class="ripple-ring r2" aria-hidden="true"></span>
                </button>
            </form>

            <div class="text-center mt-4 card-item d6" style="font-size: 11px; color: var(--text-faint); text-align: center; margin-top: 24px; display: none;">
                Queue Dashboard &copy; 2026 | Developed by <a href="https://firlli.vercel.app" target="_blank" rel="noopener noreferrer" style="color: var(--primary); font-weight: 600; text-decoration: none;">Firlli</a>
            </div>
        </div>
    </div>

    <script>
        // password visibility toggle
        (function(){
            var btn = document.getElementById('togglePass');
            var input = document.getElementById('password');
            if(!btn || !input) return;
            btn.addEventListener('click', function(){
                var isPass = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPass ? 'text' : 'password');
                btn.innerHTML = isPass
                    ? '<i class="fas fa-eye-slash" aria-hidden="true"></i>'
                    : '<i class="fas fa-eye" aria-hidden="true"></i>';
                btn.setAttribute('aria-label', isPass ? 'Sembunyikan password' : 'Tampilkan password');
            });
        })();

        // submit loading state (cosmetic; form still submits normally)
        (function(){
            var form = document.getElementById('loginForm');
            var submitBtn = form ? form.querySelector('.btn-submit') : null;
            if(!form || !submitBtn) return;
            form.addEventListener('submit', function(){
                submitBtn.classList.add('is-loading');
            });
        })();

        // live queue ticker - simulated calls, purely presentational
        (function(){
            var el = document.getElementById('ticker');
            if(!el) return;
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            var counters = ['Antrian 1','Antrian 2','Antrian 3','Antrian 4','Antrian 5','Antrian 6','Antrian 7','Antrian 8','Antrian 9','Antrian 10','Antrian 11','Antrian 12','Antrian 13','Antrian 14','Antrian 15','Antrian 16','Antrian 17','Antrian 18','Antrian 19','Antrian 20','Antrian 21','Antrian 22','Antrian 23','Antrian 24','Antrian 25','Antrian 26','Antrian 27','Antrian 28','Antrian 29','Antrian 30'];
            var prefixes = ['A','B','C','D'];
            var rows = [];
            var maxRows = 17;

            function pad(n){ return n < 10 ? '0' + n : '' + n; }

            function nextCode(){
                var p = prefixes[Math.floor(Math.random() * prefixes.length)];
                var n = Math.floor(Math.random() * 90) + 1;
                return p + '-' + pad(n);
            }

            function timeNow(){
                var d = new Date();
                return pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
            }

            function render(){
                el.innerHTML = '';
                rows.forEach(function(r, idx){
                    var row = document.createElement('div');
                    row.className = 'ticker-row';
                    row.style.animationDelay = (idx * 0.04) + 's';
                    row.style.opacity = String(1 - idx * 0.16);
                    row.innerHTML =
                        '<span class="code">' + r.code + '</span>' +
                        '<span class="where">Telah masuk ke ' + r.where + '</span>' +
                        '<span class="stamp">' + r.time + '</span>';
                    el.appendChild(row);
                });
            }

            function pushRow(){
                rows.unshift({
                    code: nextCode(),
                    where: counters[Math.floor(Math.random() * counters.length)],
                    time: timeNow()
                });
                if(rows.length > maxRows) rows.pop();
                render();
            }

            for(var i=0;i<maxRows;i++){ pushRow(); }
            if(!reduceMotion){
                setInterval(pushRow, 2600);
            }
        })();

        // metric count-up
        (function(){
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var activeEl = document.getElementById('metricActive');
            var waitEl = document.getElementById('metricWait');
            if(!activeEl || !waitEl) return;

            var targetActive = 118 + Math.floor(Math.random() * 30);
            var targetWait = (4.5 + Math.random() * 3);

            if(reduceMotion){
                activeEl.textContent = targetActive;
                waitEl.textContent = targetWait.toFixed(1) + 'm';
                return;
            }

            var steps = 36, i = 0;
            var timer = setInterval(function(){
                i++;
                var t = i / steps;
                activeEl.textContent = Math.round(targetActive * t);
                waitEl.textContent = (targetWait * t).toFixed(1) + 'm';
                if(i >= steps){
                    clearInterval(timer);
                    activeEl.textContent = targetActive;
                    waitEl.textContent = targetWait.toFixed(1) + 'm';
                }
            }, 28);
        })();
    </script>
</body>
</html>