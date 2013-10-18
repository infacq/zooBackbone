// Models
window.Makhluk = Backbone.Model.extend();

window.Zoo = Backbone.Collection.extend({
    model:Makhluk,
    url:"../api/makhluk"
});


// Views
window.ZooView = Backbone.View.extend({

    tagName:'ul',

    initialize:function () {
        this.model.bind("reset", this.render, this);
    },

    render:function (eventName) {
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
        "":"senarai",
        "makhluk/:id":"makhluk"
    },

    senarai:function () {
        this.senarai_binatang = new Zoo();
        this.s_binatang_View = new ZooView({model:this.senarai_binatang});
        this.senarai_binatang.fetch();
        $('#sidebar').html(this.s_binatang_View.render().el);
    },

    makhluk:function (id) {
        this.binatang = this.senarai_binatang.get(id);
        this.ViewBinatang = new binatangView({model:this.binatang});
        $('#content').html(this.ViewBinatang.render().el);
    }
});

var app = new AplRouter();
Backbone.history.start();