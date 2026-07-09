<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 fw-bold">Marketplace de Consultores</h1>
            <p class="text-muted">Encontre o especialista ideal para seu projeto</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="#" class="btn btn-primary btn-lg">Publicar Demanda</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Filtros</h6>
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select class="form-select" id="filter-category">
                            <option value="">Todas</option>
                            <option value="negocios">Negócios</option>
                            <option value="tecnologia">Tecnologia</option>
                            <option value="erp">ERP</option>
                            <option value="cloud">Cloud</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor / Hora</label>
                        <select class="form-select" id="filter-rate">
                            <option value="">Qualquer valor</option>
                            <option value="0-100">Até R$ 100</option>
                            <option value="100-200">R$ 100 - R$ 200</option>
                            <option value="200-500">R$ 200 - R$ 500</option>
                            <option value="500+">Acima de R$ 500</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Avaliação Mínima</label>
                        <select class="form-select" id="filter-rating">
                            <option value="">Qualquer</option>
                            <option value="4">4+ estrelas</option>
                            <option value="4.5">4.5+ estrelas</option>
                            <option value="5">5 estrelas</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" onclick="applyFilters()">Aplicar Filtros</button>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted" id="results-count">Carregando...</span>
                <select class="form-select w-auto" id="sort-by">
                    <option value="rating">Melhor Avaliado</option>
                    <option value="price_asc">Menor Preço</option>
                    <option value="price_desc">Maior Preço</option>
                    <option value="projects">Mais Projetos</option>
                </select>
            </div>
            <div id="consultants-list" class="row g-3">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Buscando consultores...</p>
                </div>
            </div>
            <nav id="pagination" class="mt-4"></nav>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    loadConsultants();

    $('#sort-by').change(function() { loadConsultants(); });

    window.applyFilters = function() {
        loadConsultants();
    };

    function loadConsultants(page = 1) {
        const params = {
            page: page,
            per_page: 12,
            sort_by: $('#sort-by').val(),
            category: $('#filter-category').val(),
            min_rating: $('#filter-rating').val(),
        };

        const rateVal = $('#filter-rate').val();
        if (rateVal) {
            const parts = rateVal.split('-');
            if (parts[0]) params.min_rate = parts[0];
            if (parts[1]) params.max_rate = parts[1];
            if (rateVal.endsWith('+')) params.min_rate = rateVal.replace('+', '');
        }

        $.ajax({
            url: cpData.restUrl + '/marketplace/search',
            method: 'GET',
            data: params,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', cpData.nonce);
            },
            success: function(resp) {
                if (resp.success) {
                    renderConsultants(resp.data);
                }
            }
        });
    }

    function renderConsultants(data) {
        const list = $('#consultants-list');
        const count = $('#results-count');

        if (!data.items || data.items.length === 0) {
            list.html('<div class="col-12 text-center py-5"><i class="bi bi-search display-1 text-muted"></i><p class="mt-3 text-muted">Nenhum consultor encontrado</p></div>');
            count.text('0 resultados');
            return;
        }

        count.text(data.total + ' resultados');

        let html = '';
        $.each(data.items, function(i, c) {
            const expertise = c.expertise ? c.expertise.map(function(e) {
                return '<span class="badge bg-light text-dark me-1">' + e.category + '</span>';
            }).join('') : '';

            const stars = '★'.repeat(Math.round(c.rating)) + '☆'.repeat(5 - Math.round(c.rating));

            html += '<div class="col-md-6 col-lg-4">';
            html += '<div class="card h-100">';
            html += '<div class="card-body">';
            html += '<div class="d-flex align-items-center mb-3">';
            html += '<img src="' + (c.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(c.display_name)) + '" class="avatar me-3" alt="' + c.display_name + '">';
            html += '<div><h6 class="mb-0 fw-bold">' + c.display_name + '</h6><small class="text-muted">' + (c.professional_title || '') + '</small></div>';
            html += '</div>';
            html += '<div class="mb-2"><span class="rating">' + stars + '</span> <small class="text-muted">' + c.rating + ' (' + c.rating_count + ')</small></div>';
            html += '<div class="mb-2">' + expertise + '</div>';
            html += '<div class="d-flex justify-content-between align-items-center mt-3">';
            html += '<span class="fw-bold text-primary">R$ ' + c.hourly_rate.toFixed(2).replace('.', ',') + '/h</span>';
            html += '<a href="/perfil-consultor/?id=' + c.id + '" class="btn btn-sm btn-outline-primary">Ver Perfil</a>';
            html += '</div></div></div></div>';
        });

        list.html(html);
        renderPagination(data.page, data.pages);
    }

    function renderPagination(current, total) {
        const nav = $('#pagination');
        if (total <= 1) { nav.html(''); return; }

        let html = '<ul class="pagination justify-content-center">';
        for (let i = 1; i <= total; i++) {
            html += '<li class="page-item ' + (i === current ? 'active' : '') + '">';
            html += '<a class="page-link" href="#" onclick="event.preventDefault(); loadConsultants(' + i + ')">' + i + '</a>';
            html += '</li>';
        }
        html += '</ul>';
        nav.html(html);
    }
});
</script>
