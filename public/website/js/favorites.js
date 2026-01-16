(function($){
  var STORAGE_KEY = 'lia:favorites';
  function load(){
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return [];
      var parsed = JSON.parse(raw);
      return Array.isArray(parsed) ? parsed.map(String) : [];
    } catch (e) {
      console.warn('Favorites load error', e);
      return [];
    }
  }
  var list = load();
  // Sync favorites from server (auth only)
  function syncFromServer(){
    var routes = (window.App && window.App.routes) || {};
    if (!routes.websiteFavoritesIds) return;
    $.ajax({ url: routes.websiteFavoritesIds, method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .done(function(res){ if (res && Array.isArray(res.ids)) { Favorites.set(res.ids); } })
      .fail(function(){ /* silent */ });
  }
  function save(){
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
    } catch (e) {
      console.warn('Favorites save error', e);
      if (window.toastr) toastr.error('\u062a\u0639\u0630\u0631 \u062d\u0641\u0638 \u0627\u0644\u0645\u0641\u0636\u0644\u0629');
    }
  }
  function notify(){
    try { localStorage.setItem(STORAGE_KEY + ':ts', Date.now().toString()); } catch(e){}
    updateAllHearts();
  }

  var Favorites = {
    get: function(){ return list.slice(); },
    has: function(id){ id = String(id); return list.indexOf(id) > -1; },
    add: function(id){ id = String(id); if (!Favorites.has(id)) { list.push(id); save(); notify(); if (window.toastr) toastr.success('\u062a\u0645 \u0627\u0636\u0627\u0641\u0629 \u0627\u0644\u0645\u0646\u062a\u062c \u0625\u0644\u0649 \u0627\u0644\u0645\u0641\u0636\u0644\u0629'); } },
    remove: function(id){ id = String(id); var idx = list.indexOf(id); if (idx > -1) { list.splice(idx,1); save(); notify(); if (window.toastr) toastr.info('\u062a\u0645 \u0625\u0632\u0627\u0644\u0629 \u0627\u0644\u0645\u0646\u062a\u062c \u0645\u0646 \u0627\u0644\u0645\u0641\u0636\u0644\u0629'); } },
    toggle: function(id){ id = String(id); if (Favorites.has(id)) Favorites.remove(id); else Favorites.add(id); },
    set: function(newList){ list = Array.from(new Set((newList || []).map(String))); save(); notify(); }
  };

  function updateHeartButton($btn, favored){
    var $icon = $btn.find('i.fa-heart');
    if (favored) {
      $btn.addClass('is-favorited');
      $icon.removeClass('far fal').addClass('fas');
      $btn.attr('aria-pressed','true');
    } else {
      $btn.removeClass('is-favorited');
      $icon.removeClass('fas fal').addClass('far');
      $btn.attr('aria-pressed','false');
    }
  }

  function updateAllHearts(){
    $('[data-favorite-toggle]').each(function(){
      var $b = $(this);
      var pid = $b.data('product-id');
      if (pid == null) return;
      updateHeartButton($b, Favorites.has(pid));
    });
  }

  function pulse($btn){
    var $icon = $btn.find('i.fa-heart');
    $icon.css({'transition':'transform 150ms ease'}).css('transform','scale(1.25)');
    setTimeout(function(){ $icon.css('transform','scale(1)'); }, 150);
  }

  $(document).on('click', '[data-favorite-toggle]', function(e){
    e.preventDefault();
    var $btn = $(this);
    if ($btn.data('loading')) return;
    var pid = $btn.data('product-id');
    if (!pid) {
      if (window.toastr) toastr.error('\u0644\u0627 \u064a\u0645\u0643\u0646 \u062a\u062d\u062f\u064a\u062f \u0627\u0644\u0645\u0646\u062a\u062c');
      return;
    }
    var routes = (window.App && window.App.routes) || {};
    var addUrl = routes.websiteFavoritesAdd;
    var removeBase = routes.websiteFavoritesRemoveBase;
    var loginUrl = routes.login || '/login';
    var isFav = $btn.hasClass('is-favorited') || Favorites.has(pid);

    // lock
    $btn.data('loading', true).prop('disabled', true).addClass('is-loading');
    var ajaxCfg = isFav
      ? { url: removeBase ? removeBase.replace('__ID__', pid) : ('/account/favourits/' + pid), method: 'DELETE' }
      : { url: addUrl || '/account/favourits', method: 'POST', data: { product_id: pid } };

  $.ajax(ajaxCfg)
      .done(function(res){
        var favState = !isFav;
        updateHeartButton($btn, favState);
        if (favState) { Favorites.add(pid); if (window.toastr) toastr.success('\u062a\u0645 \u0627\u0636\u0627\u0641\u0629 \u0627\u0644\u0645\u0646\u062a\u062c \u0625\u0644\u0649 \u0627\u0644\u0645\u0641\u0636\u0644\u0629'); }
        else {
          Favorites.remove(pid);
          if (window.toastr) toastr.info('\u062a\u0645 \u0625\u0632\u0627\u0644\u0629 \u0627\u0644\u0645\u0646\u062a\u062c \u0645\u0646 \u0627\u0644\u0645\u0641\u0636\u0644\u0629');
          // If we are on the favourites page, remove the product card immediately
          var $grid = $btn.closest('.products-grid');
          if ($grid.length && $grid.attr('id') === 'favorites-page-grid') {
            var $item = $btn.closest('.product-item');
            if ($item.length) { $item.remove(); }
            // Show empty-state message if no items remain
            if ($grid.find('.product-item').length === 0) {
              var emptyMsg = $grid.data('empty-msg') || 'Favorites list is empty.';
              $grid.html('<div class="alert alert-info text-center">' + escapeHtml(emptyMsg) + '</div>');
            }
          }
        }
        pulse($btn);
      })
      .fail(function(xhr){
        if (xhr && xhr.status === 401) { window.location.href = loginUrl; return; }
        if (window.toastr) toastr.error('\u062d\u062f\u062b \u062e\u0637\u0623 \u0623\u062b\u0646\u0627\u0621 \u062a\u062d\u062f\u064a\u062b \u0627\u0644\u0645\u0641\u0636\u0644\u0629');
      })
      .always(function(){ $btn.data('loading', false).prop('disabled', false).removeClass('is-loading'); });
  });

  // storage sync across tabs
  window.addEventListener('storage', function(ev){
    if (ev.key === STORAGE_KEY || ev.key === STORAGE_KEY + ':ts') {
      list = load();
      updateAllHearts();
      if ($('#favorites-grid').length) { renderFavoritesGrid(); }
    }
  });

  function renderFavoritesGrid(){
    var $grid = $('#favorites-grid');
    if (!$grid.length) return;
    var ids = Favorites.get();
    $grid.empty();
    if (!ids.length) {
      $grid.html('<div class="alert alert-info text-center">\u0642\u0627\u0626\u0645\u0629 \u0627\u0644\u0645\u0641\u0636\u0644\u0629 \u0641\u0627\u0631\u063a\u0629.</div>');
      return;
    }
    $grid.addClass('block-relative').append('<div class="page-blocker"><span class="spinner"></span></div>');
    var base = (window.App && window.App.routesClientProductShowBase) || '/client/products/';
    var requests = ids.map(function(id){ return $.get(base + id).then(function(res){ return (res && res.data) ? res.data : res; }).catch(function(){ return null; }); });
    Promise.all(requests).then(function(items){
      $grid.empty();
      items.forEach(function(p){ if (!p) return; $grid.append(buildProductCard(p)); });
      updateAllHearts();
    }).catch(function(){
      $grid.html('<div class="alert alert-danger text-center">\u062a\u0639\u0630\u0631 \u062a\u062d\u0645\u064a\u0644 \u0627\u0644\u0645\u0641\u0636\u0644\u0629 \u062d\u0627\u0644\u064a\u0627\u064b.</div>');
    }).finally(function(){ $grid.find('.page-blocker').remove(); });
  }

  function buildProductCard(p){
    var discountPercent = p.discount_percentage || p.discount || 0;
    var maxQty = p.quantity || 1;
    var priceCurrent = Number(p.final_price || p.price || 0).toFixed(2);
    var priceOld = Number(p.base_price || p.old_price || 0).toFixed(2);
    var productUrl = (window.App && window.App.routes && window.App.routes.websiteProductShow) ? window.App.routes.websiteProductShow.replace('__ID__', p.id) : ('/product/' + p.id);
    var imgUrl = p.image_url || p.image;

    var $card = $('<div class="product-item"></div>');
    $card.append(
      '<a href="'+productUrl+'" class="item-img" aria-label="'+escapeHtml(p.name)+'">' +
      '<img loading="lazy" src="'+imgUrl+'" class="img-contain" alt="'+escapeHtml(p.name)+'" />' +
      '</a>'
    );

    var sarIcon = (window.App && window.App.assetsSarIcon) || '/website/images/icons/sar.svg';
    var pricesHtml = '<strong class="price-current">'+priceCurrent+'<span class="curreny"><img loading="lazy" src="'+sarIcon+'" alt="sar" class="svg" /></span></strong>';
    if (discountPercent > 0 && priceOld > 0) {
      pricesHtml += '<span class="price-discount"> -'+discountPercent+'% </span>' +
                    '<del class="price-old">'+priceOld+'<span class="curreny"><img loading="lazy" src="'+sarIcon+'" alt="sar" class="svg" /></span></del>';
    }

    var info =
      '<div class="item-info">' +
        '<h3 class="item-title"><a href="'+productUrl+'" aria-label="'+escapeHtml(p.name)+'">'+escapeHtml(p.name)+'</a></h3>' +
        '<div class="item-prices">'+pricesHtml+'</div>' +
        '<div class="item-tools">' +
          '<div class="item-quantity">' +
            '<input type="number" class="quantity-input" min="1" max="'+maxQty+'" value="1" readonly />' +
            '<button class="quantity-btn plus-btn" type="button" onclick="this.parentNode.querySelector(\'input[type=number]\').stepUp()"><i class="fal fa-plus"></i></button>' +
            '<button class="quantity-btn minus-btn" type="button" onclick="this.parentNode.querySelector(\'input[type=number]\').stepDown()"><i class="fal fa-minus"></i></button>' +
          '</div>' +
          '<button class="item-button add-to-cart-btn" data-product-id="'+p.id+'"><i class="fal fa-shopping-cart"></i></button>' +
          '<button class="item-fav" data-favorite-toggle data-product-id="'+p.id+'"><i class="far fa-heart"></i></button>' +
        '</div>' +
      '</div>';

    $card.append(info);
    return $card;
  }

  function escapeHtml(s){ return String(s || '').replace(/[&<>"']/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#039;'})[c]; }); }

  $(function(){
    syncFromServer();
    renderFavoritesGrid();
    updateAllHearts();
  });

  // expose
  window.Favorites = Favorites;

})(jQuery);