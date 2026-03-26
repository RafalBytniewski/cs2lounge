<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CS2 Lounge Trade</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=rajdhani:400,500,600,700|teko:400,500,600" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="page-shell">
            <header class="topbar">
                <div class="brand-lockup">
                    <div class="brand-mark">CS2</div>
                    <div>
                        <p class="brand-name">Trade Lounge</p>
                        <p class="brand-tag">publiczne oferty wymiany skinow</p>
                    </div>
                </div>

                <nav class="top-nav" aria-label="Main navigation">
                    <a href="#offers">Browse offers</a>
                    <a href="#create-trade">Create trade</a>
                    <a href="#inventory">My inventory</a>
                    <a href="#roadmap">Roadmap</a>
                </nav>

                <div class="top-actions">
                    <div class="status-pill">{{ number_format($tradeOffers->count()) }} public offers</div>
                    <a class="sign-in" href="#">Steam sync later</a>
                </div>
            </header>

            <main class="layout-grid trade-layout">
                <aside class="rail left-rail">
                    <section class="panel section-stack">
                        <div class="panel-heading">
                            <h2>Trade Filters</h2>
                        </div>
                        <a class="menu-link active" href="#offers">All public offers <span>{{ $stats['publicOffers'] }}</span></a>
                        <a class="menu-link" href="#offers">Any knife <span>{{ $stats['knifeRequests'] }}</span></a>
                        <a class="menu-link" href="#offers">Any skins <span>{{ $stats['anySkinTrades'] }}</span></a>
                        <a class="menu-link" href="#create-trade">Create trade <span>{{ $inventoryItems->count() }}</span></a>
                        <a class="menu-link" href="#inventory">Inventory ready <span>{{ $inventoryItems->count() }}</span></a>
                    </section>

                    <section class="panel section-stack">
                        <div class="panel-heading">
                            <h2>Quick Wants</h2>
                        </div>
                        <div class="tag-grid">
                            <span class="filter-tag {{ old('want_type', 'any-skins') === 'any-skins' ? 'active' : '' }}">any skins</span>
                            <span class="filter-tag {{ old('want_type') === 'any-knife' ? 'active' : '' }}">any knife</span>
                            <span class="filter-tag {{ old('want_type') === 'specific-item' ? 'active' : '' }}">specific item</span>
                            <span class="filter-tag {{ old('want_type') === 'upgrade-only' ? 'active' : '' }}">upgrade only</span>
                        </div>
                    </section>

                    <section class="panel section-stack">
                        <div class="panel-heading">
                            <h2>How It Works</h2>
                        </div>
                        <ul class="feed-list">
                            <li><span class="feed-time">1</span> wybierasz skiny z eq</li>
                            <li><span class="feed-time">2</span> ustawiasz czego szukasz</li>
                            <li><span class="feed-time">3</span> publikujesz oferte publiczna</li>
                            <li><span class="feed-time">4</span> drugi user wysyla trade offer</li>
                        </ul>
                    </section>
                </aside>

                <section class="content-column">
                    <section class="hero panel trade-hero">
                        <div>
                            <p class="eyebrow">Trade only app</p>
                            <h1>Publiczne oferty wymiany skin za skin.</h1>
                            <p class="hero-copy">To juz nie jest sam mock. Formularz nizej zapisuje publiczna oferte do SQLite, blokuje wybrane skiny jako wystawione i pokazuje je na liscie ofert.</p>
                        </div>
                        <div class="hero-stats">
                            <div>
                                <strong>{{ $stats['publicOffers'] }}</strong>
                                <span>public offers</span>
                            </div>
                            <div>
                                <strong>{{ $stats['knifeRequests'] }}</strong>
                                <span>knife requests</span>
                            </div>
                            <div>
                                <strong>{{ $stats['anySkinTrades'] }}</strong>
                                <span>any skin trades</span>
                            </div>
                        </div>
                    </section>

                    @if (session('status'))
                        <div class="flash-message flash-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="flash-message flash-error">
                            <strong>Form validation failed.</strong>
                            <ul class="error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <section class="panel section-stack trade-builder" id="create-trade">
                        <div class="panel-heading">
                            <h2>Create Public Trade</h2>
                            <a href="#inventory">available inventory: {{ $inventoryItems->count() }}</a>
                        </div>

                        <form method="POST" action="{{ route('offers.store') }}" class="trade-form">
                            @csrf

                            <div class="builder-grid">
                                <section class="trade-side" id="inventory">
                                    <div class="trade-side-head">
                                        <h3>You Offer</h3>
                                        <span>select one or more skins from inventory</span>
                                    </div>

                                    <div class="skin-grid">
                                        @forelse ($inventoryItems as $item)
                                            <label class="skin-card selectable">
                                                <input
                                                    class="skin-check"
                                                    type="checkbox"
                                                    name="inventory_item_ids[]"
                                                    value="{{ $item->id }}"
                                                    {{ in_array((string) $item->id, array_map('strval', old('inventory_item_ids', [])), true) ? 'checked' : '' }}
                                                >
                                                <span class="skin-rarity {{ strtolower($item->rarity) === 'covert' ? 'covert' : (strtolower($item->rarity) === 'classified' ? 'classified' : '') }}"></span>
                                                <p class="skin-name">{{ $item->name }}</p>
                                                <p class="skin-meta">{{ $item->weapon_type }} @if($item->exterior) • {{ $item->exterior }} @endif</p>
                                                <p class="skin-price">${{ number_format((float) $item->estimated_value, 2) }}</p>
                                            </label>
                                        @empty
                                            <article class="skin-card ghost">
                                                <p class="skin-placeholder">No available inventory items left. Re-seed or add inventory sync next.</p>
                                            </article>
                                        @endforelse
                                    </div>
                                </section>

                                <div class="trade-arrow">for</div>

                                <section class="trade-side">
                                    <div class="trade-side-head">
                                        <h3>You Want</h3>
                                        <span>preset albo konkretny item</span>
                                    </div>

                                    <div class="want-options">
                                        <label class="want-choice">
                                            <input type="radio" name="want_type" value="any-skins" {{ old('want_type', 'any-skins') === 'any-skins' ? 'checked' : '' }}>
                                            <span class="want-pill">any skins</span>
                                        </label>
                                        <label class="want-choice">
                                            <input type="radio" name="want_type" value="any-knife" {{ old('want_type') === 'any-knife' ? 'checked' : '' }}>
                                            <span class="want-pill">any knife</span>
                                        </label>
                                        <label class="want-choice">
                                            <input type="radio" name="want_type" value="specific-item" {{ old('want_type') === 'specific-item' ? 'checked' : '' }}>
                                            <span class="want-pill">specific item</span>
                                        </label>
                                        <label class="want-choice">
                                            <input type="radio" name="want_type" value="upgrade-only" {{ old('want_type') === 'upgrade-only' ? 'checked' : '' }}>
                                            <span class="want-pill">upgrade only</span>
                                        </label>
                                    </div>

                                    <div class="form-stack">
                                        <label class="field-group">
                                            <span>Specific item label</span>
                                            <input
                                                class="text-input"
                                                type="text"
                                                name="want_label"
                                                value="{{ old('want_label') }}"
                                                placeholder="np. Butterfly Knife Fade"
                                            >
                                        </label>

                                        <label class="field-group">
                                            <span>Trade details</span>
                                            <input
                                                class="text-input"
                                                type="text"
                                                name="want_details"
                                                value="{{ old('want_details') }}"
                                                placeholder="np. or equal value skins / exact float later"
                                            >
                                        </label>

                                        <label class="field-group">
                                            <span>Public note</span>
                                            <textarea
                                                class="text-input text-area"
                                                name="notes"
                                                rows="3"
                                                placeholder="Optional note visible with the offer."
                                            >{{ old('notes') }}</textarea>
                                        </label>
                                    </div>
                                </section>
                            </div>

                            <div class="builder-footer">
                                <div class="trade-summary">
                                    <strong>Offer summary</strong>
                                    <p>Wybrane skiny zostana oznaczone jako wystawione i pokazane na publicznej liscie ofert.</p>
                                </div>
                                <button class="action-link primary-link button-reset" type="submit">Publish public offer</button>
                            </div>
                        </form>
                    </section>

                    <section class="panel section-stack" id="offers">
                        <div class="panel-heading">
                            <h2>Live Public Offers</h2>
                            <a href="/">refresh</a>
                        </div>

                        <div class="offer-list">
                            @forelse ($tradeOffers as $offer)
                                <article class="offer-card">
                                    <div class="offer-user">
                                        <div class="avatar">{{ strtoupper(substr($offer->user->name, 0, 1)) }}</div>
                                        <div>
                                            <p class="profile-name">{{ $offer->user->name }}</p>
                                            <p class="profile-subtitle">{{ $offer->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>

                                    <div class="offer-trade">
                                        <div class="offer-block">
                                            <span class="offer-label">has</span>
                                            <p>{{ $offer->inventoryItems->pluck('name')->join(' + ') }}</p>
                                            <small>
                                                {{ $offer->inventoryItems->map(fn ($item) => trim($item->weapon_type.' '.($item->exterior ?? '')))->join(' • ') }}
                                            </small>
                                        </div>
                                        <div class="offer-swap">for</div>
                                        <div class="offer-block wanted-block">
                                            <span class="offer-label">wants</span>
                                            <p>{{ $offer->want_label }}</p>
                                            <small>{{ $offer->want_details ?: ($offer->notes ?: 'public trade request') }}</small>
                                        </div>
                                    </div>

                                    <a class="action-link" href="#">Send trade</a>
                                </article>
                            @empty
                                <article class="skin-card ghost">
                                    <p class="skin-placeholder">No public offers yet.</p>
                                </article>
                            @endforelse
                        </div>
                    </section>
                </section>

                <aside class="rail right-rail">
                    <section class="panel section-stack">
                        <div class="panel-heading">
                            <h2>Profile Snapshot</h2>
                        </div>
                        <div class="profile-box">
                            <div class="avatar">{{ strtoupper(substr($currentUser->name, 0, 1)) }}</div>
                            <div>
                                <p class="profile-name">{{ $currentUser->name }}</p>
                                <p class="profile-subtitle">{{ $currentUser->email }}</p>
                            </div>
                        </div>
                        <div class="profile-metrics">
                            <div><strong>{{ $inventoryItems->count() }}</strong><span>available skins</span></div>
                            <div><strong>{{ $currentUser->tradeOffers()->count() }}</strong><span>active offers</span></div>
                            <div><strong>{{ $tradeOffers->count() }}</strong><span>market offers</span></div>
                        </div>
                    </section>

                    <section class="panel section-stack">
                        <div class="panel-heading">
                            <h2>Request Types</h2>
                        </div>
                        <article class="bet-row">
                            <div>
                                <p>Any skins</p>
                                <span>generic and fast trade requests</span>
                            </div>
                            <strong class="positive">{{ $stats['anySkinTrades'] }}</strong>
                        </article>
                        <article class="bet-row">
                            <div>
                                <p>Any knife</p>
                                <span>classic lounge style request</span>
                            </div>
                            <strong class="positive">{{ $stats['knifeRequests'] }}</strong>
                        </article>
                        <article class="bet-row">
                            <div>
                                <p>Specific item</p>
                                <span>exact target skin or wear</span>
                            </div>
                            <strong>{{ $tradeOffers->where('want_type', 'specific-item')->count() }}</strong>
                        </article>
                    </section>

                    <section class="panel section-stack" id="roadmap">
                        <div class="panel-heading">
                            <h2>Roadmap</h2>
                        </div>
                        <div class="notice warning">
                            <strong>Step 1 done</strong>
                            <p>demo inventory, public offer model i zapis do bazy</p>
                        </div>
                        <div class="notice">
                            <strong>Step 2 next</strong>
                            <p>autentyczny sync inventory Steam i login</p>
                        </div>
                        <div class="notice">
                            <strong>Step 3 later</strong>
                            <p>filtrowanie po float, wear, typie itemu i real send trade links</p>
                        </div>
                    </section>
                </aside>
            </main>
        </div>
    </body>
</html>
