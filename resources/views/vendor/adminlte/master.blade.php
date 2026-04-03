@include('layouts.search')

<script>
// فتح البحث بـ Ctrl+K أو Click على أيقونة البحث
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openSearch();
    }
    if (e.key === 'Escape') closeSearch();
});

// اعتراض زر البحث ديال AdminLTE
document.addEventListener('DOMContentLoaded', function() {
    var searchBtn = document.querySelector('.nav-link[data-widget="navbar-search"]');
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openSearch();
        });
    }
});

function openSearch() {
    document.getElementById('global-search-wrapper').style.display = 'block';
    setTimeout(() => document.getElementById('global-search-input').focus(), 100);
}

function closeSearch() {
    document.getElementById('global-search-wrapper').style.display = 'none';
    document.getElementById('global-search-input').value = '';
    document.getElementById('search-results').innerHTML =
        '<p class="text-muted text-center py-3 mb-0"><i class="fas fa-keyboard"></i> Tapez au moins 2 caractères...</p>';
}

// البحث الفوري
let searchTimer;
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('global-search-input').addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = this.value.trim();

        if (q.length < 2) {
            document.getElementById('search-results').innerHTML =
                '<p class="text-muted text-center py-3 mb-0"><i class="fas fa-keyboard"></i> Tapez au moins 2 caractères...</p>';
            return;
        }

        document.getElementById('search-results').innerHTML =
            '<p class="text-center py-3 mb-0"><i class="fas fa-spinner fa-spin"></i> Recherche...</p>';

        searchTimer = setTimeout(() => {
            fetch('/search?q=' + encodeURIComponent(q), {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    document.getElementById('search-results').innerHTML =
                        '<p class="text-muted text-center py-3 mb-0"><i class="fas fa-search"></i> Aucun résultat trouvé</p>';
                    return;
                }

                var html = '';
                data.forEach(function(t) {
                    html += `
                    <a href="${t.url}" onclick="closeSearch()"
                       style="display:flex; align-items:center; gap:12px;
                              padding:10px 12px; border-radius:6px; color:inherit;
                              text-decoration:none; border-bottom:1px solid #f0f0f0"
                       onmouseover="this.style.background='#f8f9fa'"
                       onmouseout="this.style.background='none'">
                        <div style="width:40px; height:40px; border-radius:50%;
                                    background:${t.validated ? '#28a745' : '#6c757d'};
                                    display:flex; align-items:center; justify-content:center;
                                    color:white; font-weight:bold; flex-shrink:0">
                            ${t.name.charAt(0)}
                        </div>
                        <div style="flex:1">
                            <div style="font-weight:500">${t.name}</div>
                            <div style="font-size:12px; color:#6c757d">
                                CIN: ${t.cin} | CEF: ${t.cef} | ${t.filiere}
                            </div>
                        </div>
                        <div style="text-align:right; flex-shrink:0">
                            ${t.validated
                                ? '<span style="background:#d4edda;color:#155724;padding:2px 8px;border-radius:12px;font-size:11px"><i class="fas fa-check"></i> Validé</span>'
                                : '<span style="background:#e2e3e5;color:#383d41;padding:2px 8px;border-radius:12px;font-size:11px">En cours</span>'
                            }
                            <div style="font-size:11px;color:#6c757d;margin-top:2px">${t.docs_count} doc(s)</div>
                        </div>
                    </a>`;
                });

                document.getElementById('search-results').innerHTML = html;
            });
        }, 300);
    });
});
</script>