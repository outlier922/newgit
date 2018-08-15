(function ($) {
    var plugin_name = "dptk_combobind",
        data_key = "dptk_combobind";
    var defaults = {
        value_field: 'id',
        show_field: 'name',
        combobind_class : 'combobind-select',
        arrow_class : 'combobind-arrow',
        arrow_down_class : 'combobind-arrow-down',
        arrow_up_class : 'combobind-arrow-up',
        dropdown_class : 'combobind-dropdown',
        dropdown_opened_class: 'combobind-dropdown-opened',
        item_class: 'option-item',
        item_selected_class: 'option-selected',
        item_hover_class: 'option-hover',
        input_class: 'combobind-input',
        combobind_focus_class: 'combobind-focus'
    };

    function Plugin ( element, options ) {
        this._name = plugin_name;
        this.el = element;
        this.$el = $(element);
        if(this.$el.prop('multiple')) return;

        this.settings = $.extend({}, defaults, options, this.$el.data());
        this._defaults = defaults;
        this.$options = this.$el.find('option');

        this.init();

        $.fn[ plugin_name ].instances.push(this);//操作一个时，控制其它的状态
    };

    $.extend(Plugin.prototype, {
        init: function () {
            this._construct();
            this._events();
        },
        _construct: function () {
            var self = this;
            //添加外部容器
            this.$container = this.$el.wrapAll('<div class="' + this.settings.combobind_class + '" />').parent();
            //从select元素的中的data-container_style中拷贝style
            if(this.settings.container_style){
                this.$container.attr('style', this.settings.container_style);
            }
            if(this.settings.container_class){
                this.$container.addClass(this.settings.container_class);
            }

            //添加箭头
            this.$arrow = $('<div class="' + this.settings.arrow_class + ' ' + this.settings.arrow_down_class + '" />').appendTo(this.$container);

            //添加下拉框
            this.$dropdown = $('<ul class=" ' + this.settings.dropdown_class + ' " style="z-index:100; "/>').appendTo(this.$container);
            //添加input
            this.$input = $('<input type="text" placeholder="' + (this.settings.placeholder ? this.settings.placeholder : '') + '" class="'+ this.settings.input_class + '">').appendTo(this.$container);

            this._init_data();
        },
        _events: function () {
            this.$container.on('focus.input', 'input', $.proxy(this._focus, this));
            this.$container.on('mouseup.input', 'input',function (e) {
                e.preventDefault();
            });
            this.$container.on('blur.input', 'input', $.proxy(this._blur, this));
            this.$el.on('change.select', $.proxy(this._change, this))

            this.$container.on('keyup', 'input', $.proxy(this._keyup, this));
            this.$container.on('combobind:open', $.proxy(this._open, this));
            this.$container.on('combobind:close', $.proxy(this._close, this));
            this.$container.on('click.item', '.'+this.settings.item_class, $.proxy(this._select, this));

            this.$container.on('click.arrow', '.'+this.settings.arrow_class , $.proxy(this._toggle, this));
        },
        _init_data: function (keyword) {
            this._init_data_status = 'run';
            this._updateData(keyword);
        },
        _reload_data: function (keyword) {
            this._updateData(keyword);
        },

        _updateData: function (keyword) {
            //ajax加载数据select数据
            var self = this;
            keyword = keyword ? keyword : '';
            $.post(this.$el.data('init_url'),{keyword:keyword},function (rec) {
                // if(!isJsonStr(rec)){
                //     alert("数据格式不正确，请检测返回数据"); return;
                // }

                rec = $.parseJSON(rec);
                var info = rec.infor, o='';

                for(var i=0,node; node=info[i]; i++){
                    o += "<option value='"+node[self.settings.value_field]+"'>"+node[self.settings.show_field]+"</option>";
                }

                self.$el.html(o);
                self.options = o;
                self.$options = self.$el.find('option');

                //设置被选中的值
                if(self.settings.value){
                    self.$options.each(function (index,node) {
                        if($(node).prop('value') == self.settings.value){
                            self.$el.prop('selectedIndex',index);
                        }
                    });
                }
                else if(self.settings.default_value){
                    self.$options.each(function (index,node) {
                        if($(node).prop('value') == self.settings.default_value){
                            self.$el.prop('selectedIndex',index);
                        }
                    });
                }
                else{
                    //假如没有选中值时，input直接显示placeholder
                    self.$el.prop('selectedIndex',-1);
                }

                var o = '', k = 0, p = '';//
                self.selectedIndex = self.$el.prop('selectedIndex');
                self.$options.each(function(i, e){
                    if(!e.value) p = e.innerHTML;
                    o += '<li class="'+ self.settings.item_class + ' ' + (k == self.selectedIndex ? self.settings.item_selected_class : '') + '" '
                        + 'data-index="' + k + '" '
                        + 'data-value="' + this.value + '">'
                        + this.innerHTML + '</li>';
                    k++;
                });
                self.$dropdown.html(o);
                self.$items = self.$dropdown.children();

                //第一次加载数据时，input标签显示被选中的值
                if(self._init_data_status == 'run'){
                    self._update_input();
                    self._init_data_status = 'completed';
                }
            });
        },
        _keyup: function () {
            var keyword = $.trim(this.$input.val());
            if(keyword != this.old_keyword){
                this._reload_data(keyword);
                this.old_keyword = keyword;
            }
        },
        _focus:function () {
            this.$container.trigger("combobind:open");
        },
        _change: function () {
            this._update_input();
        },
        _open:function () {
            var self = this;
            this.opened = true;
            this.$dropdown.addClass(this.settings.dropdown_opened_class);
            $.each($.fn[ plugin_name ].instances, function(i, plugin){
                if(plugin != self ) plugin.$container.trigger('combobind:close');
            });
        },
        _close:function () {
            this.opened = false;
            this.$dropdown.removeClass(this.settings.dropdown_opened_class);
        },
        _toggle: function(){
            this.opened? this._close.call(this) : this._open.call(this);
        },
        _select:function (event) {
            var item = event.currentTarget? $(event.currentTarget) : $(event);
            if(!item.length) return;

            var index = item.data('index');
            this._selectByIndex(index);
            this.$container.trigger('combobind:close');
        },
        _selectByIndex: function (index) {
            if(typeof index == 'undefined') index = 0;
            this.$el.prop('selectedIndex', index).trigger('change');
        },
        _update_input: function(){
            var selected = this.$el.prop('selectedIndex');
            if(this.$el.val()){
                var text = this.$el.find('option').eq(selected).text();
                this.$input.val(text);
            }
            else{
                this.$input.val('');
            }

            this.$items.removeClass(this.settings.item_selected_class)
                .filter(function () {
                    return $(this).data('index') == selected
                })
                .addClass(this.settings.item_selected_class);

        }
    });

    $.fn[plugin_name] = function ( options, args ) {
        this.each(function(){
            $.data( this, "plugin_" + data_key, new Plugin( this, options ) );
        });
        return this;
    };
    $.fn[ plugin_name ].instances = [];

})($);
