Event.observe(window, "load", function () {
        $$('#wishlist-table .btn-remove').each(function (element) {
            var replacedHref = element.href.replace('/wishlist/','/finalwishlist/');
            element.writeAttribute("href", replacedHref);
        })
});