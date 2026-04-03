<div id="global-search-wrapper" style="position:relative; display:none">
    <div style="position:fixed; top:0; left:0; width:100%; height:100%;
                background:rgba(0,0,0,0.5); z-index:9998"
         id="search-overlay" onclick="closeSearch()">
    </div>
    <div style="position:fixed; top:80px; left:50%; transform:translateX(-50%);
                width:600px; max-width:95vw; z-index:9999; background:white;
                border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.3)">
        <div style="padding:16px; border-bottom:1px solid #eee">
            <div style="display:flex; align-items:center; gap:10px">
                <i class="fas fa-search text-primary fa-lg"></i>
                <input type="text"
                       id="global-search-input"
                       placeholder="Rechercher par CIN, CEF, Nom, Prénom..."
                       style="border:none; outline:none; font-size:16px; flex:1"
                       autocomplete="off">
                <button onclick="closeSearch()"
                        style="border:none; background:none; font-size:18px; cursor:pointer; color:#999">
                    &times;
                </button>
            </div>
        </div>
        <div id="search-results" style="max-height:400px; overflow-y:auto; padding:8px">
            <p class="text-muted text-center py-3 mb-0" id="search-hint">
                <i class="fas fa-keyboard"></i> Tapez au moins 2 caractères...
            </p>
        </div>
    </div>
</div>