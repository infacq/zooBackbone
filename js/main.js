// Models
window.Makhluk = Backbone.Model.extend();

window.Zoo = Backbone.Collection.extend({
    model:Makhluk,
    url:"./api/makhluk",
    initialize: function() {        
				// tugaskan Deferred yang diarahkan oleh fetch() sebagai salah satu sifatnya (property)
        this.deferred = this.fetch();
    }
});


// Views
window.ZooView = Backbone.View.extend({

    tagName:'ul',

    initialize:function () {
        _.bindAll(this,'render');
				this.model.on("reset", this.render, this);				
    },

    render:function () {
				_.each(this.model.models, function (binatang) {
						$(this.el).append(new ViewItemZoo({model:binatang}).render().el);
				}, this);
        return this;
    }

});

window.ViewItemZoo = Backbone.View.extend({

    tagName:"li",

    template:_.template($('#tpl-senarai-binatang-item').html()),
		
    render:function (eventName) {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    }

});

window.binatangView = Backbone.View.extend({

    template:_.template($('#tpl-binatang-rinci').html()),

    render:function (eventName) {        
				$(this.el).html(this.template(this.model.toJSON()));
        return this;
    }

});


// Router
var AplRouter = Backbone.Router.extend({

    routes:{
        ""         : "senarai",
        "zoo/:id" : "makhluk"
    },

		initialize: function() {
				this.senarai_binatang = new Zoo();
				this.senarai_binatang.fetch();
		},
		
    senarai:function () {        
        var _this = this; //terpaksa menggunakan _this kerana lingkungan berubah mengikut arahan dibawah
				this.senarai_binatang.deferred.done(function() {
						// jQuery telah lama memperkenalkan kaedah deffered ini bagi mengatasi kerumitan masalah asyncronize						
						_this.s_binatang_View = new ZooView({model:_this.senarai_binatang}); 								
						$('#sidebar').html(_this.s_binatang_View.render().el);
				});
    },

    makhluk:function (id) {				
        var _this = this;
				this.senarai_binatang.deferred.done(function() {
						var makhluk = _this.senarai_binatang.get(id);
						_this.ViewBinatang = new binatangView({model:makhluk});
						$('#content').html(_this.ViewBinatang.render().el);
				});
    }
});

var app = new AplRouter();
Backbone.history.start();