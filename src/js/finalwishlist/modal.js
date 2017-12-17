ModalWindow = Class.create({
	initialize: function(){
		this.cont = "";
		this.overlay = "";
		this.win = "";
		this.container = new Element('div', {id:'modal-container'});
		var container = this.container;
		$(document.body).insert({bottom:container});
	},
	show: function(element, overlay){
		this.close();
		this.cont = element;
		if(overlay) this.overlay = this.container.appendChild(new Element('div', {'class':'modal-overlay'}));
		this.win = this.container.appendChild(new Element('div', {'class':'modal-window'}));
		this.win.insert({bottom:this.cont});
		var notAllowBtn = new Element('button',{id:'allow-finalwishlist-no',class:'button'}).update("<span>don't allow</span>");
		var allowBtn = new Element('button',{id:'allow-finalwishlist-yes',class:'button'}).update("<span>allow</span>");
		this.win.insert({bottom:notAllowBtn});
		this.win.insert({bottom:allowBtn});
	},
	close: function(e){
		if(e) e.stop();
		this.container.childElements().invoke('remove');
	}
});

Event.observe(window, "load", function () {
    if(window.isLoggedIn != undefined){
        var lastclickedElement;
        var wishlistForm;
        $$('.add-to-links .link-wishlist').each(function (element) {
            if (element.onclick !== null) {
                element.writeAttribute("onclick", 'return false;');
                element.observe('click', respondProductPage);
                //jQuery("#allow-finalwishlist").on("hidden.bs.modal", sendWishlist);
            } else {
                element.writeAttribute("onclick", "respondProductList('"+ element.href +"');");
                element.href = "#";
            }
        })
    }
});

function respondProductPage(event) {
    window.lastclickedElement = this;
    modal = new ModalWindow();
    tmpelement = new Element('div').update($('allow-finalwishlist').innerHTML);
    modal.show(tmpelement, true);
    $('allow-finalwishlist-no').observe("click", sendWishlist);
    $('allow-finalwishlist-yes').observe("click", sendToFinalwishlist);
	$$('#modal-container .modal-header .close').first().observe("click", function(){
		modal.close()
	});
    event.preventDefault();
}

function respondProductList(addlink) {
    lastclickedElement = addlink;
    window.wishlistForm = new Element( 'form',
                                   {
                                     method: 'post',
                                     action: document.origin + '/finalwishlist/index/add'
                                   });

    modal = new ModalWindow();
    tmpelement = new Element('div').update($('allow-finalwishlist').innerHTML);
    modal.show(tmpelement, true);
    //jQuery('#allow-finalwishlist').modal('show');
    //jQuery("#allow-finalwishlist").on("hidden.bs.modal", sendFormWishlist);
    $('allow-finalwishlist-no').observe("click", sendFormWishlist);
    $('allow-finalwishlist-yes').observe("click", sendFormtoFinalWishlist);
	$$('#modal-container .modal-header .close').first().observe("click", function(){modal.close()});
}

function sendFormWishlist(){
    window.wishlistForm.action = lastclickedElement;

    $(document.body).insert(window.wishlistForm);
    window.wishlistForm.submit();
}
function sendFormtoFinalWishlist(){
    window.wishlistForm.insert(new Element('input', {type: 'hidden', name: 'finalwishlist', value: lastclickedElement
}));
    $(document.body).insert(window.wishlistForm);
    window.wishlistForm.submit();
}
function sendWishlist() {
    productAddToCartForm.submitLight(window.lastclickedElement, window.lastclickedElement.href)
}
function sendToFinalwishlist() {
    $('product_addtocart_form').insert(new Element('input', {type: 'hidden', name: 'finalwishlist', value
: window.lastclickedElement.href}));
    productAddToCartForm.submitLight(window.lastclickedElement, document.origin + '/finalwishlist/index/add')
}