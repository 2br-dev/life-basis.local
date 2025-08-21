(function(n,p,u){var w=n([]),s=n.resize=n.extend(n.resize,{}),o,l="setTimeout",m="resize",t=m+"-special-event",v="delay",r="throttleWindow";s[v]=250;s[r]=true;n.event.special[m]={setup:function(){if(!s[r]&&this[l]){return false}var a=n(this);w=w.add(a);n.data(this,t,{w:a.width(),h:a.height()});if(w.length===1){q()}},teardown:function(){if(!s[r]&&this[l]){return false}var a=n(this);w=w.not(a);a.removeData(t);if(!w.length){clearTimeout(o)}},add:function(b){if(!s[r]&&this[l]){return false}var c;function a(d,h,g){var f=n(this),e=n.data(this,t);e.w=h!==u?h:f.width();e.h=g!==u?g:f.height();c.apply(this,arguments)}if(n.isFunction(b)){c=b;return a}else{c=b.handler;b.handler=a}}};function q(){o=p[l](function(){w.each(function(){var d=n(this),a=d.width(),b=d.height(),c=n.data(this,t);if(a!==c.w||b!==c.h){d.trigger(m,[c.w=a,c.h=b])}});q()},s[v])}})(jQuery,this);(function(b){var a={};function c(f){function e(){var h=f.getPlaceholder();if(h.width()==0||h.height()==0){return}f.resize();f.setupGrid();f.draw()}function g(i,h){i.getPlaceholder().resize(e)}function d(i,h){i.getPlaceholder().unbind("resize",e)}f.hooks.bindEvents.push(g);f.hooks.shutdown.push(d)}b.plot.plugins.push({init:c,options:a,name:"resize",version:"1.0"})})(jQuery);
/**
* Plugin, активирующий виджет Динамика продаж
*/
$.widget('rs.rsSellChart', {
    options: {
        yearFilter: '.year-filter',
        yearCheckbox: '.year-filter input',
        yearCheckboxLabel: '.year-filter label',
        placeholder: '.placeholder',
        yearPlotOptions: {
            xaxis: {
                minTickSize: [1, "month"],
            }
        },
        monthPlotOptions: {
            xaxis: {
                minTickSize: [1, "day"],
            }
        },
        plotOptions: {
            xaxis: {
                mode: 'time',
                monthNames: lang.t("янв,фев,мар,апр,май,июн,июл,авг,сен,окт,ноя,дек").split(',')
            },
            yaxis: {
                tickDecimals:0
            },
            lines: { show: true },
            points: { show: true },
            legend: {
                show: true,
                noColumns: 1, // number of colums in legend table
                margin: 5, // distance from grid edge to default legend container within plot
                backgroundColor: '#fff', // null means auto-detect
                backgroundOpacity: 0.85 // set to 0 to avoid background
            },
            grid: {
                hoverable: true,
                borderWidth: 0,
                borderColor: '#e5e5e5'
            },
            hooks: {
                processRawData: function(plot, series, data, datapoints) {
                    var seriesData = [];
                    $.each(data, function(key, val) {
                        seriesData.push([val.x, val.y]);
                    });

                    series.originalData = $.extend({}, data);
                    series.data = seriesData;
                }

            }
        }
    },

    _create: function() {
        var _this = this;
        this.chart = $(this.options.placeholder, this.element);

        this.element
            .on('change', this.options.yearCheckbox, function() {
                _this.build();
            })
            .on('click', this.options.yearFilter, function(e) {e.stopPropagation();});

        this.build();
        this.chart.on("plothover", function(event, pos, item) {
            _this._plotHover(event, pos, item);
        });
    },

    build: function() {
        var _this = this,
            dataset = [],
            yearList = $(this.options.yearCheckbox + ':checked', this.element);

        if (yearList.length) {
            yearList.each(function() {
                var key = $(this).val();
                if (key && _this.chart.data('inlineData').points[key])
                    dataset.push(_this.chart.data('inlineData').points[key]);
            });
        } else {
            dataset = this.chart.data('inlineData').points;
        }

        if (dataset.length > 0) {
            $.plot(this.chart, dataset, $.extend(true, this.options.plotOptions, this.options[this.chart.data('inlineData').range+'PlotOptions']));
        }
    },

    _plotHover: function(event, pos, item) {
        if (item) {
            if (this.previousPoint != item.dataIndex) {
                this.previousPoint = item.dataIndex;
                var
                    pointData = item.series.originalData[item.dataIndex],
                    dateStr = this[('_' + this.chart.data('inlineData').range + 'Format')].call(this, pointData);

                var tooltipText = lang.t('Заказов ')+dateStr+': <strong>'+pointData.count+'</strong> <br\> ' + lang.t('На сумму') + ': <strong>'+this.numberFormat(pointData.total_cost,2,',',' ')+' '+this.chart.data('inlineData').currency+'</strong>';
                this._showTooltip(item.pageX, item.pageY, tooltipText);
            }
        }
        else {
            $("#sellChartTooltip").remove();
            this.previousPoint = null;
        }
    },

    _showTooltip: function(x, y, contents) {
        $("#sellChartTooltip").remove();
        $('<div id="sellChartTooltip" class="chart-tooltip"/>').html(contents).css( {
            top: y + 10,
            left: x + 10
        }).appendTo("body").fadeIn(200);
    },

    _yearFormat: function(pointData) {
        var
            months = lang.t("январе,феврале,марте,апреле,мае,июне,июле,августе,сентябре,октябре,ноябре,декабре").split(','),
            pointDate = new Date(pointData.pointDate);

        return lang.t('в %date', {date: months[pointDate.getMonth()] + ' ' + pointDate.getFullYear()});
    },

    _monthFormat: function(pointData) {
        var
            months = lang.t("января,февраля,марта,апреля, мая,июня,июля,августа,сентября,октября,ноября,декабря").split(','),
            pointDate = new Date(pointData.x);

        return pointDate.getDate()+' '+months[pointDate.getMonth()]+' '+pointDate.getFullYear();
    },

    numberFormat: function(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };

            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
                .length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1)
                .join('0');
        }
        return s.join(dec);
    }
});
/**
 * Формирование вывода цен в отформтированном виде
 *
 * @param number - цифры для преобразования
 * @param decimals - количество дробных чисел
 * @param dec_point - разделитель дробных
 * @param thousands_sep - разделитель тысячных
 * @return {string}
 */
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '')
        .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
                .toFixed(prec);
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
        .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
            .join('0');
    }
    return s.join(dec).replace(dec_point+'00', '');
}


/**
 * Показ круговых диаграмм
 *
 * @param target - блок где будет показываться график
 * @param data - данные круговой диаграммы
 * @param unit - единица измерения
 */
function statisticShowPlot(target, data, unit)
{
    $.plot(target + ' .graph', data, {
        series: {
            pie: {
                show: true,
                label: {
                    show: false,
                    radius: 3/4,
                    formatter: function(label, series) {
                        if(label === null) return "NULL";
                        var title = label;
                        if(label.length > 20){ label = label.substring(0,20) + '..'; }
                        var unit = ' '+series.pie.unit;
                        var out = '<div title="'+title+' : '+number_format(series.data[0][1], 2, ',', ' ')+unit+'" style=";text-align:center;padding:2px;color:white;">';
                        out += label;
                        out += '<br/>';
                        out += Math.round(series.percent)+'%';
                        //out += series.data[0][1];
                        out += '</div>';
                        return out;
                    },
                    background: {
                        opacity: 0.5,
                        color: '#000'
                    }
                },
                unit: unit
            }
        },
        grid: {
            hoverable: true,
            clickable: true
        },
        legend: {
            container: target + ' .flc-plot',
            backgroundOpacity: 0.5,
            noColumns: 2,
            backgroundColor: "white",
            lineWidth: 0
        },
        tooltip: true,
        tooltipOpts: {
            content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
            shifts: {
                x: 20,
                y: 0
            },
            defaultTheme: false,
            cssClass: 'chart-tooltip'
        },
        xaxis:{ labelWidth:5 }
    });
}

/**
 * Показ графика ключевых показателей в виде столбиков
 *
 * @param target - блок где будет показываться график
 * @param json_bars - json с панелями графика (список справа с заголовками линий)
 * @param json_values - json значения для посотроения графика
 * @param json_ticks - json подписи к столбикам
 */
function statisticShowKeyIndicator(target, json_bars, json_values, json_ticks)
{
    // Диаграмма в виде столбиков
    $(function () {
        $.plot(target, json_bars, {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.15,
                    align: "center",
                    order: 1
                },
                absoluteValues: json_values
            },
            xaxis: {
                mode: "categories",
                ticks: json_ticks,
                tickLength: 1
            },
            grid: {
                hoverable: true,
                borderWidth:0
            },
            yaxis: {
                allowDecimals: false
            }
        });
    });

    var previousPoint;
    var showTooltip = function(x, y, contents) {
        $("#sellChartTooltip").remove();
        $('<div id="sellChartTooltip" class="chart-tooltip"/>').html(contents).css( {
            top: y + 10,
            left: x + 10
        }).appendTo("body").fadeIn(200);
    };

    /**
     * Наведение на подсказки на графике
     */
    $(".graph").bind("plothover", function(e, pos, item) {
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;                        
                var
                    pointData = item.series.absoluteValues[item.seriesIndex][item.dataIndex];
                    
                var tooltipText = pointData;
                showTooltip(item.pageX, item.pageY, tooltipText);
            }
        }
        else {
            $("#sellChartTooltip").remove();
            previousPoint = null;            
        }
    });
}

/**
 * Показ статистики в виде волн
 *
 * @param target - блок где будет показываться график
 * @param wrapper - блок с отображаением
 * @param json_values - json значения для построения графика
 */
function statisticShowWaves(target, wrapper, json_values)
{
    var currentWave = $("#presetWaves a.act", $(wrapper)).data('value');
    //Определим нужные данные
    var current_data = json_values[currentWave];

    $(function() {

        //Данные по X
        var x_data = [];
        $.each(current_data['data']['ticks'], function(i, v){
            x_data.push([i, v]);
        });
        //Данные по Y
        var y_data = [];
        $.each(current_data['data']['values'], function(i, v){
            y_data.push([i, v]);
        });

        var plot = $.plot(target, [
            {
                data: y_data,
                label: current_data['label'],
                color: 'rgb(203,75,75)'
            }
        ], {
            series: {
                lines: {
                    show: true
                },
                points: {
                    show: true
                }
            },
            xaxis: {
                ticks: x_data,
                tickLength: 1
            },
            grid: {
                hoverable: true,
                clickable: true,
                borderWidth: 0
            }
        });

        var previousPoint;
        var showTooltip = function(x, y, contents) {
            $("#sellChartTooltip").remove();
            $('<div id="sellChartTooltip" class="chart-tooltip"/>').html(contents).css( {
                top: y + 10,
                left: x + 10
            }).appendTo("body").fadeIn(200);
        };

        /**
         * Наведение на подсказки на графике
         */
        $(".graph", $(wrapper)).on("plothover", function(e, pos, item) {
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    var tooltipText = current_data['data']['values'][item.datapoint[0]] + " " + current_data['y_unit'] + "<br/>" + current_data['data']['ticks'][item.datapoint[0]];
                    showTooltip(item.pageX, item.pageY, tooltipText);
                }
            }
            else {
                $("#sellChartTooltip").remove();
                previousPoint = null;
            }
        });
    });
}
$(function() {
    /**
     * Инициализация фильтров в графике статистики
     *
     */
    var initDateSelector = function() {
        /**
         * Инициализация селектора дат
         */
        $('.stat-date-range input[datefilter]', this).dateselector();

        /**
         * Переключение заранее заготовленных пресетов фильтров
         */
        $(".stat-date-range .date-presets a", this).off('click.date-range').on('click.date-range', function(){
            var context = $(this).closest('.stat-date-range');
            var from    = $(this).data('from');
            var to      = $(this).data('to');
            
            var input_from = $("input.from", context);
            var input_to   = $("input.to", context);
            
            $('input.hidden', context).remove();
            input_from.clone().attr('type', 'hidden').val(from).addClass('.hidden').appendTo(context);
            input_from.clone().attr('type', 'hidden').val(to).addClass('.hidden').appendTo(context);
            
            input_from.attr('disabled', true);
            input_to.attr('disabled', true);
            $("input[type=submit]", context).click();
        });

        /**
         * Переключение типа группировки
         */
        $(".stat-date-range .date-presets-groups a", this).off('click.date-groups').on('click.date-groups', function(){
            var context = $(this).closest('.stat-date-range');
            var wrapper = $(this).closest('.dropdown');
            var block_wrapper = $(this).closest('.updatable');
            var value   = $(this).data('value');

            if (value == 'week'){
                $('.graph', block_wrapper).addClass('weekly');
            }else{
                $('.graph', block_wrapper).removeClass('weekly');
            }

            $('input[type="hidden"]', wrapper).val(value);
            $("input[type=submit]", context).click();
        });
    };
    
    $.contentReady(initDateSelector);
});
/*
 * Flot plugin to order bars side by side.
 * 
 * Released under the MIT license by Benjamin BUFFET, 20-Sep-2010.
 *
 * This plugin is an alpha version.
 *
 * To activate the plugin you must specify the parameter "order" for the specific serie :
 *
 *  $.plot($("#placeholder"), [{ data: [ ... ], bars :{ order = null or integer }])
 *
 * If 2 series have the same order param, they are ordered by the position in the array;
 *
 * The plugin adjust the point by adding a value depanding of the barwidth
 * Exemple for 3 series (barwidth : 0.1) :
 *
 *          first bar décalage : -0.15
 *          second bar décalage : -0.05
 *          third bar décalage : 0.05
 *
 */

(function($){
    function init(plot){
        var orderedBarSeries;
        var nbOfBarsToOrder;
        var borderWidth;
        var borderWidthInXabsWidth;
        var pixelInXWidthEquivalent = 1;
        var isHorizontal = false;

        /*
         * This method add shift to x values
         */
        function reOrderBars(plot, serie, datapoints){
            var shiftedPoints = null;
            
            if(serieNeedToBeReordered(serie)){                
                checkIfGraphIsHorizontal(serie);
                calculPixel2XWidthConvert(plot);
                retrieveBarSeries(plot);
                calculBorderAndBarWidth(serie);
                
                if(nbOfBarsToOrder >= 2){  
                    var position = findPosition(serie);
                    var decallage = 0;
                    
                    var centerBarShift = calculCenterBarShift();

                    if (isBarAtLeftOfCenter(position)){
                        decallage = -1*(sumWidth(orderedBarSeries,position-1,Math.floor(nbOfBarsToOrder / 2)-1)) - centerBarShift;
                    }else{
                        decallage = sumWidth(orderedBarSeries,Math.ceil(nbOfBarsToOrder / 2),position-2) + centerBarShift + borderWidthInXabsWidth*2;
                    }

                    shiftedPoints = shiftPoints(datapoints,serie,decallage);
                    datapoints.points = shiftedPoints;
               }
           }
           return shiftedPoints;
        }

        function serieNeedToBeReordered(serie){
            return serie.bars != null
                && serie.bars.show
                && serie.bars.order != null;
        }

        function calculPixel2XWidthConvert(plot){
            var gridDimSize = isHorizontal ? plot.getPlaceholder().innerHeight() : plot.getPlaceholder().innerWidth();
            var minMaxValues = isHorizontal ? getAxeMinMaxValues(plot.getData(),1) : getAxeMinMaxValues(plot.getData(),0);
            var AxeSize = minMaxValues[1] - minMaxValues[0];
            pixelInXWidthEquivalent = AxeSize / gridDimSize;
        }

        function getAxeMinMaxValues(series,AxeIdx){
            var minMaxValues = new Array();
            for(var i = 0; i < series.length; i++){
                minMaxValues[0] = series[i].data[0][AxeIdx];
                minMaxValues[1] = series[i].data[series[i].data.length - 1][AxeIdx];
            }
            return minMaxValues;
        }

        function retrieveBarSeries(plot){
            orderedBarSeries = findOthersBarsToReOrders(plot.getData());
            nbOfBarsToOrder = orderedBarSeries.length;
        }

        function findOthersBarsToReOrders(series){
            var retSeries = new Array();

            for(var i = 0; i < series.length; i++){
                if(series[i].bars.order != null && series[i].bars.show){
                    retSeries.push(series[i]);
                }
            }

            return retSeries.sort(sortByOrder);
        }

        function sortByOrder(serie1,serie2){
            var x = serie1.bars.order;
            var y = serie2.bars.order;
            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
        }

        function  calculBorderAndBarWidth(serie){
            borderWidth = serie.bars.lineWidth ? serie.bars.lineWidth  : 2;
            borderWidthInXabsWidth = borderWidth * pixelInXWidthEquivalent;
        }
        
        function checkIfGraphIsHorizontal(serie){
            if(serie.bars.horizontal){
                isHorizontal = true;
            }
        }

        function findPosition(serie){
            var pos = 0
            for (var i = 0; i < orderedBarSeries.length; ++i) {
                if (serie == orderedBarSeries[i]){
                    pos = i;
                    break;
                }
            }

            return pos+1;
        }

        function calculCenterBarShift(){
            var width = 0;

            if(nbOfBarsToOrder%2 != 0)
                width = (orderedBarSeries[Math.ceil(nbOfBarsToOrder / 2)].bars.barWidth)/2;

            return width;
        }

        function isBarAtLeftOfCenter(position){
            return position <= Math.ceil(nbOfBarsToOrder / 2);
        }

        function sumWidth(series,start,end){
            var totalWidth = 0;

            for(var i = start; i <= end; i++){
                totalWidth += series[i].bars.barWidth+borderWidthInXabsWidth*2;
            }

            return totalWidth;
        }

        function shiftPoints(datapoints,serie,dx){
            var ps = datapoints.pointsize;
            var points = datapoints.points;
            var j = 0;           
            for(var i = isHorizontal ? 1 : 0;i < points.length; i += ps){
                points[i] += dx;
                //Adding the new x value in the serie to be abble to display the right tooltip value,
                //using the index 3 to not overide the third index.
                serie.data[j][3] = points[i];
                j++;
            }

            return points;
        }

        plot.hooks.processDatapoints.push(reOrderBars);

    }

    var options = {
        series : {
            bars: {order: null} // or number/string
        }
    };

    $.plot.plugins.push({
        init: init,
        options: options,
        name: "orderBars",
        version: "0.2"
    });

})(jQuery);


/**
* Plugin, активирующий автоматическую транслитерацию поля
* @author ReadyScript lab.
*/
(function($){
    $.fn.autoTranslit = function(method) {
        var defaults = {
            formAction: 'form[action]',
            context:'form, .virtual-form',
            virtualForm: '.virtual-form',
            addPredicate: 'add',
            targetName: null,
            showUpdateButton: true
        }, 
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('autoTranslit');
            
            var methods = {
                init: function(initoptions) {                    
                    if (data) return;
                    data = {}; $this.data('autoTranslit', data);
                    data.options = $.extend({}, defaults, initoptions);
                    if ($this.data('autotranslit')) {
                        data.options.targetName = $this.data('autotranslit');
                    }
                    data.options.target = $('input[name="'+data.options.targetName+'"]', $(this).closest(data.options.context));
                    if (data.options.target) {
                        //Подключаем автоматическую транслитерацию, если происходит создание объекта
                        var isAdd;
                        if ($this.closest(data.options.virtualForm).length) {
                            isAdd = $this.closest(data.options.virtualForm).data('isAdd');
                        } else {
                            let action = $this.closest(data.options.formAction).attr('action');
                            if (action) {
                                isAdd = action.toLowerCase().indexOf(data.options.addPredicate) > -1;
                            }

                        }
                        if (isAdd) {
                            $this.on('blur', onBlur);
                        }
                        if (data.options.showUpdateButton) {
                            var update = $('<a class="update-translit"></a>').click(onUpdateTranslit).attr('title', lang.t('Транслитерировать заново'));
                            $(data.options.target).after(update).parent().trigger('new-content');
                        }
                    }
                }
            };
            
            //private 
            var onBlur = function() {
                if (data.options.target.val() == '') {
                    onUpdateTranslit();
                }
            },
            onUpdateTranslit = function() {
                data.options.target.val( translit( $this.val() ) );
            },
            translit = function( text ) {
                let diacritical_characters = ('Ä ä À à Á á Â â Ã ã Å å Ǎ ǎ Ą ą Ă ă Æ æ Ā ā Ç ç Ć ć Ĉ ĉ Č č Ď đ Đ ď ð È'+
                    ' è É é Ê ê Ë ë Ě ě Ę ę Ė ė Ē ē Ĝ ĝ Ģ ģ Ğ ğ Ĥ ĥ Ì ì Í í Î î Ï ï ı Ī ī Į į Ĵ ĵ Ķ ķ Ĺ ĺ Ļ ļ Ł ł Ľ ľ '+
                    'Ñ ñ Ń ń Ň ň Ņ ņ Ö ö Ò ò Ó ó Ô ô Õ õ Ő ő Ø ø Œ œ Ŕ ŕ Ř ř ẞ ß Ś ś Ŝ ŝ Ş ş Š š Ș ș Ť ť Ţ ţ Þ þ Ț ț Ü'+
                    ' ü Ù ù Ú ú Û û Ű ű Ũ ũ Ų ų Ů ů Ū ū Ŵ ŵ Ý ý Ÿ ÿ Ŷ ŷ Ź ź Ž ž Ż ż').split(' '),
                    transform_characters   = ('a a a a a a a a a a a a a a a a a a ae ae a a c c c c c c c c d d d d d'+
                    ' e e e e e e e e e e e e e e e e g g g g g g h h i i i i i i i i i i i i i j j k k l l l l l l l '+
                    'l n n n n n n n n o o o o o o o o o o o o o o oe oe r r r r s s s s s s s s s s s s t t t t th th'+
                    ' t t u u u u u u u u u u u u u u u u u u w w y y y y y y z z z z z z').split(' ');

                var rus = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о',
                    'п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я','+'].concat(diacritical_characters);

                var eng = ['a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k',
                    'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh',
                    'sch', '', 'y', '', 'e', 'yu', 'ya','-plus-'].concat(transform_characters);

                var result = '', char;
                var hyphen = false;
                for(var i=0; i<text.length; i++) {
                    char = text.toLowerCase().charAt(i);

                    if (char.match(/[a-z0-9]/gi)) {
                        result = result + char;
                        hyphen = false;
                    } else {
                        var pos = rus.indexOf(char);
                        if (pos > -1) {
                            result = result + eng[pos];
                            hyphen = false;
                        } else if (!hyphen) {
                            result = result + '-';
                            hyphen = true;
                        }
                    }
                }

                //Вырезаем по краям знак минуса "-"
                result = result.replace(/^\-+|\-+$/g, '');
                result = result.replace(/\-\-/g, '-');

                return result;
            };
            
            
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

    $.contentReady(function() {
        $('input[data-autotranslit]', this).autoTranslit();
    });

})(jQuery);
/**
 * Плагин обеспечивает отображение всплывающих сообщений в адмнистративной панели.
 * Пример вызова:
 * $.messenger('hello!'); //Отобразит обычное сообщение
 * $.messenger('show', {text: 'Hello!', theme: 'error'}); //Отобразит сообщение в красном окне
 *
 * @author ReadyScript lab.
 */
(function($) {
    $.messenger = function(method) {
        var defaults = {
            offsetY: 70, //Стартовое смещение по Y
            msg: {
                timer: null,
                theme: '', //Класс сообщений
                distance: 10, //Расстояние между сообщениями
                expire: 20, //В секундах время отображения сообщения
                stopExpireOnClick: true
            }
        }, 
        args = arguments,
        
        $this = $('#messages-container');
        if (!$this.length) {
            $this = $('<div id="messages-container"></div>').appendTo('body');
        }
        var data = $this.data('messenger');
        if (!data) { //Инициализация
            data = {
                options: defaults
            }; 
            $this.data('messenger', data);
        }
        
        var methods = {
            
            show: function(parameters) {
                var $box = getMessageBox(parameters);
                var local_params = $.extend({}, data.options.msg, parameters);
                $box.data('messenger', local_params);
                
                var offset = +(defaults.offsetY);
                var messages = $('.message-box', $this);
                for( var i=messages.length-1; i>=0; i-- ) {
                    offset = offset + $(messages[i]).height() + (local_params.distance);
                }
                
                $box.css({
                    bottom: offset+'px'
                })
                $box
                    .hover(function() {
                        local_params.pause = true;
                    }, function() {
                        local_params.pause = false;
                    })
                    .on('messenger.close', closeBox)
                    .on('click.messenger', '.close', function() {
                        $(this).closest('.message-box').trigger('messenger.close');
                    })
                    .appendTo($this).fadeIn();
                    
                if (local_params.stopExpireOnClick) {
                    $box.on('mousedown.messenger', stopExpire);
                }
                
                if (local_params.expire) {                    
                    local_params.timer = setTimeout(function() {
                        $box.trigger('messenger.close');
                        if (!local_params.pause) {
                            $box.trigger('messenger.close');
                        } else {
                            $box.one('mouseleave.messengerOne', function() {
                                $box.trigger('messenger.close');
                            });
                        }
                    }, local_params.expire * 1000);
                }
            },
            
            update: function() {
                
                var messages = $('.message-box', $this);
                var newOffset = {};
                
                var offset = +(defaults.offsetY);
                newOffset["0"] = offset;
                for( var i=0; i<messages.length; i++ ) {
                    offset = offset + $(messages[i]).height() + ($(messages[i]).data('messenger').distance);
                    newOffset[i+1] = offset;
                }
                
                messages.each(function(i) {
                    $(this).animate({
                        bottom: newOffset[i]+'px'
                    }, 'fast');
                });
            },
            
            hideAll: function() {
                $('.message-box', $this).trigger('messenger.close');
            },
            
            setOptions: function(options) {
                data.options = $.extend(data.options, options);
            }
        }
        
        //private 
        var getMessageBox = function(parameters) {
            return $('<div class="message-box"></div>')
                    .append('<a class="close"></a>')
                    .append($('<div class="msg"></div>').html(parameters.text))
                    .addClass(parameters.theme)
                    .hide();
        },
        
        stopExpire = function() {
            var box = $(this);
            clearTimeout(box.data('messenger').timer);
            box.unbind('.messengerOne');
        },

        closeBox = function() {
            var box = $(this);
            clearTimeout(box.data('messenger').timer);
            box.fadeOut('fast', function() {
                box.remove();
                methods.update();
            });
        };
        
        
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object') {
            return methods.init.apply( this, args );
        } else {
            var params = Array.prototype.slice.call( args, 1 );
            var extend = {text: method};
            if (!params[0]) params[0] = {};
            params[0] = $.extend(params[0], extend);
            methods['show'].apply( this, params );
        }
    }

})(jQuery);
/**
 * Plugin позволяет проводить виртуальные обучающие туры по админ. панели ReadyScript
 *
 * @author ReadyScript lab.
 */
(function($) {
    $.tour = function(method) {
        var
            defaults = {
                startTourButton: '.start-tour',
                baseUrl: '/',
                folder: '',
                adminSection: global.adminSection,
                tipInfoCorrectionY:20,
            },
            args = arguments,
            timeoutHandler;

        var data = $('body').data('tour');
        if (!data) { //Инициализация
            data = {
                options: defaults
            };
            $('body').data('tour', data);
        }

        //public
        var
            methods = {
                init: function(tours, localOptions) {
                    data.tours = tours;
                    data.options = $.extend({}, data.options, localOptions);

                    $(data.options.startTourButton).click(function() {
                        methods.start($(this).data('tourId'), 'index', true);
                    });
                    //Если тур был запущен раннее, то пытаем определить действие
                    var tourId = $.cookie('tourId');
                    if (tourId) methods.start(tourId);

                },
                start: function(tour_id, startStep, force) {
                    if (!data.tours[tour_id]) {
                        console.log('Tour '+tour_id+' not found');
                        return;
                    };
                    $.cookie('tourId', tour_id, {path:'/'});
                    data.tour = data.tours[tour_id];
                    data.tourTotalSteps = getTotalSteps();
                    data.tourStepIndex = [];
                    $.each(data.tour, function(i, val) {
                        data.tourStepIndex.push(i);
                    });

                    var
                        step = findStep(data.tour, startStep, force);

                    //Проверка: если step = false, то значит стартовая страница не соответствует туру.
                    if (step) {
                        runStep(step);
                    } else {
                        if (step !== null) {
                            methods.stop();
                        }
                    }

                    $('body').bind('keypress.tour', function(e){
                        if (e.keyCode == 27) methods.stop();
                    });
                },
                stop: function() {
                    $.cookie('tourId', null, {path:'/'});
                    $.cookie('tourStep', null, {path:'/'});
                    hideStep();
                },
            }

        //private
        var
            /**
             * Выполняет поиск текущего шага в туре по принципу:
             * текущий URL должен совпадать с URL, заявленным в шаге
             *
             * @param tour
             */
            findStep = function(tour, step, force) {
                if (!step) step = $.cookie('tourStep');
                if (!step && !$('#debug-top-block').is('.debug-mobile')) {
                    step = 'index';
                }
                if (!data.tour[step]) return false;

                //Проверяем соответствует ли шаг тура текущей странице
                var a = $('<a />').attr('href', location.href).get(0);
                var relpath = ('/'+a.pathname.replace(/^([/])/gi, '')) + a.search;
                var relpath_mask = relpath.replace(data.options.adminSection, '%admin%').replace(/([/])$/gi, '');

                var steppath;
                if (step) {
                    steppath = data.options.folder + data.tour[step].url.replace(/([/])$/gi, '');
                }

                if (relpath_mask != steppath && !force) {
                    foundStep = false;
                    //Пытаемся найти шаг, по URL.
                    var before, found;
                    for(var key in data.tour) {
                        if (data.options.folder + data.tour[key].url.replace(/([/])$/gi, '') == relpath_mask) {
                            if (!before || before == step) { //Этот шаг идет вслед за предыдущим отображенным
                                //Мы нашли шаг по URL, возвращаем его
                                foundStep = key;
                                break;
                            }
                        }
                        before = key;
                    }

                    //Если не нашли, то выводим сообщение о прерывании тура
                    if (!foundStep) {
                        showDialog({
                            type: 'dialog',
                            message: lang.t('Вы перешли на страницу, не предусмотренную интерактивным курсом. <br>Вернуться и продолжить обучение?'),
                            buttons: {
                                yes: step,
                                no: false
                            }
                        });
                        return null;
                    }
                    step = foundStep;
                }

                return step;
            },

            getStepIndex = function(step) {
                var i = 1;
                for(var key in data.tour) {
                    if (key == step) return i;
                    i++;
                }
                return false;
            },

            getTotalSteps = function() {
                var i = 0;
                for(var key in data.tour) i++;
                return i;
            },

            runStep = function(step, noRedirect) {
                var tourStep = data.tour[step];
                hideStep();

                data.curStep = step;
                data.curStepIndex = getStepIndex(step);
                $.cookie('tourStep', step, {path:'/'});

                //Проверим, соответствует ли текущая страница шагу step
                var a = $('<a />').attr('href', location.href).get(0);
                var relpath = ('/'+a.pathname.replace(/^([/])/gi, '')) + a.search;
                var relpath_mask = relpath.replace(data.options.adminSection, '%admin%').replace(/([/])$/gi, '');
                if (relpath_mask != data.options.folder + tourStep.url.replace(/([/])$/gi, '') && !noRedirect) {
                    //Необходим переход на другую страницу
                    $.rs.loading.show();
                    location.href = data.options.folder + tourStep.url.replace('%admin%', data.options.adminSection);
                    return;
                }

                //Выполняет один шаг обучения
                var type = (tourStep.type) ? tourStep.type : 'tip';
                if (tourStep.onStart) tourStep.onStart();
                switch (type) {
                    case 'dialog': showDialog(tourStep); break;
                    case 'tip': showTip(step); break;
                    case 'info': showInfo(step); break;
                    case 'form': showForm(step); break;
                }

                //Выполняем watch
                if (tourStep.watch) {
                    $('body').on(tourStep.watch.event + '.tour', tourStep.watch.element, function() {
                        runAction(tourStep.watch.next, true);
                    });
                }

                $('a[data-step]').click(function() {
                    runAction( $(this).data('step') );
                });
            },

            overlayShow = function(blur) {
                if (blur) {
                    $('body > *').addClass('filterBlur');
                }
                $('<div id="tourOverlay"></div>').appendTo('body');

            },

            overlayHide = function() {
                $('#tourOverlay').remove();
                $('body > *').removeClass('filterBlur');
            },

            showDialog = function(tourStep) {
                overlayShow(true);
                var dialog = $('<div id="tipDialog" />').addClass('tipDialog').append('<a class="tipDialogClose" />')
                var content = $('<div class="tipContent" />').html(tourStep.message);
                var buttons = $('<div class="tipButtons" />');

                $.each(tourStep.buttons, function(key, val) {
                    var button = $('<a class="tipButton"/>');
                    var buttonText = (typeof(val) == 'object' && val.text) ? val.text : false;

                    switch(key) {
                        case 'no': {
                            button.text(buttonText ? buttonText : lang.t('Нет')).addClass('tipNo');
                            break;
                        }
                        case 'yes': {
                            button.text(buttonText ? buttonText : lang.t('Да')).addClass('tipYes');
                            break;
                        }
                        case 'finish': {
                            button.text(buttonText ? buttonText : lang.t('Завершить')).addClass('tipYes');
                            break;
                        }
                        default: {
                            button.text(buttonText).attr(val.attr);
                        }
                    }
                    button.click(hideDialog);

                    //Переход на следующий шаг
                    if (typeof(val) == 'string' || typeof(val) == 'boolean' || typeof(val) == 'object') {
                        button.click(function() {
                            var next = (typeof(val) == 'object') ? val.step : val;
                            runAction(next);
                        });
                    }

                    $('.tipDialogClose', dialog).click(methods.stop);
                    $('#tourOverlay').click(methods.stop);

                    button.appendTo(buttons);
                });

                dialog
                    .append(content)
                    .append(buttons)
                    .appendTo('body')
                    .addClass('flipInX animated');

                dialog.css({
                    marginLeft: -parseInt(dialog.width()/2),
                    marginTop:-parseInt(dialog.height()/2),
                });

            },

            showTip = function(step)
            {
                var
                    tourStep = data.tour[step];

                tourStep.tip = $.extend(true, {
                    correctionX: 0,
                    correctionY: 0,
                    animation: 'fadeInDown',
                    css: {
                        'minWidth': 280
                    }
                }, tourStep.tip);


                if (tourStep.tip.fixed) {
                    tourStep.tip.css['position'] = 'fixed';
                }

                var element = [];

                if (typeof(tourStep.tip.element) == 'object') {

                    for(var i=0; i<tourStep.tip.element.length; i++) {
                        var currentElement = tourStep.tip.element[i];

                        if (typeof(currentElement) == 'string') {
                            var selector = currentElement;
                        } else {
                            var selector = currentElement.selector;
                        }

                        element = $(selector).first();

                        if (currentElement.whenUse && currentElement.whenUse(element)) {
                            break;
                        } else if (!currentElement.whenUse && element.is(':visible')) {
                            break;
                        }
                    }

                    if (typeof(currentElement) == 'object') {
                        //Объединяем параметры конкретного элемента с общими
                        tourStep.tip = $.extend(tourStep.tip, currentElement);
                    }
                } else {
                    element = $(tourStep.tip.element).first();
                }

                if (!element.length) {
                    if (tourStep.tip.notFound) {
                        runAction(tourStep.tip.notFound);
                    }
                    return;
                }

                var tip = $('<div class="tipTour" />')
                tip.html('<div class="tipContent">'+tourStep.tip.tipText+'</div>')
                    .append(getStatusLine())
                    .append('<i class="corner"/>')
                    .css(tourStep.tip.css)
                    .appendTo('body')
                    .data('originalWidth', tip.width())
                    .width(tip.width())
                    .draggable();

                getTipPosition(element, tourStep.tip, tip);

                scrollWindow(tip);

                $(window).bind('resize.tour', function() {
                    getTipPosition(element, tourStep.tip, tip);
                });

                if (tourStep.tip.animation) {
                    tip.addClass(tourStep.tip.animation + ' animated');
                }

                if (tourStep.whileTrue) {
                    var whileTrue = function() {
                        if (!tourStep.whileTrue()) {
                            goNext();
                        } else {
                            timeoutHandler = setTimeout(whileTrue, 2000);
                        }
                    }();

                    timeoutHandler = setTimeout(whileTrue, 2000);
                }

                if (tourStep.checkTimeout) {
                    timeoutHandler = setTimeout(goNext, tourStep.checkTimeout);
                }
            },

            getTipPosition = function(element, tipData, tip)
            {

                var position = {
                        top: element.offset().top + element.innerHeight() + 10,
                        left: element.offset().left + element.width()/2,
                    },
                    bodyWidth = $('body').width();

                if (tipData.bottom) {
                    //Выноска находится внизу экрана
                    position.top = element.offset().top - getHeight(tip);
                    tip.addClass('bottom');
                }

                if (tipData.left) {
                    //Выноска находится внизу экрана
                    position.top = element.offset().top;
                    position.left = element.offset().left - getWidth(tip) - 10;
                    tip.addClass('left');
                }

                var tipWidth = getWidth(tip);

                if (tipWidth > bodyWidth-20) {
                    tip.width( bodyWidth-40 );
                    tipWidth = bodyWidth-20;
                }

                if (position.left + tipWidth > bodyWidth) {
                    position.marginLeft = -(position.left + tipWidth - bodyWidth + 10);
                } else {
                    position.marginLeft = 0;
                }
                position.left = position.left + tipData.correctionX;
                position.top = position.top + tipData.correctionY;

                if (position.left < 0) {
                    tip.width( tip.width() + position.left );
                    position.left = 0;
                }

                tip.css(position);

                //Устанавливаем смещение выноски
                tip.find('.corner').css('marginLeft', -position.marginLeft);


            },

            runAction = function(action, noRedirect) {

                switch(typeof(action)) {

                    case 'boolean': if (!action) {
                        methods.stop();
                    }; break;
                    case 'string': runStep(action, noRedirect); break;
                    case 'function': {
                        var result = action();
                        if (result) runStep(result, noRedirect);
                        if (result === false) return false;
                    }
                    default: return false;
                }
                return true;
            },

            closeFormDialog = function() {
                if (data.curStep && data.tour[data.curStep].type == 'form') {
                    //Пытаемся закрыть окно, если текущий шаг связан с формой
                    $('body').off('dialogBeforeDestroy.tour');
                    $('.dialog-window').dialog('close');
                }
            },

            goNext = function() {
                if (data.curStepIndex < data.tourTotalSteps) {
                    closeFormDialog();
                    runStep(data.tourStepIndex[data.curStepIndex]);
                }
            },

            goPrev = function() {
                if (data.curStepIndex > 1) {
                    closeFormDialog();
                    runStep(data.tourStepIndex[data.curStepIndex-2]);
                }
            },

            scrollWindow = function(oneTip) {

                if (oneTip.closest('.dialog-window').length) {
                    var $window = oneTip.closest('.contentbox');
                    var $windowHeight = $window.height() - 55;
                    var $scrollElement = $window;

                    var tipOffsetTop = oneTip.offset().top - 90 + $scrollElement.scrollTop();

                } else {
                    var $window = $(window);
                    var $windowHeight = $window.height();
                    var $scrollElement = $('html, body');

                    var tipOffsetTop = oneTip.offset().top - 90;
                }

                //Если tip не помещается на экран, то перемещаем scroll
                if ( tipOffsetTop < $window.scrollTop()
                    || tipOffsetTop > $window.scrollTop() + $windowHeight
                ) {
                    $scrollElement.animate({
                        scrollTop: tipOffsetTop - 50
                    });
                }
            },

            showForm = function(step)
            {
                var tourStep = data.tour[step],
                    checkTimeout,
                    tipMap = {};

                data.curSubStep = 0;
                data.totalSubSteps = 0;

                //Создаем массив tip.label => index, для быстрого нахождения index по label.
                $.each(tourStep.tips, function(i, tip) {
                    if (tip.label) {
                        tipMap[tip.label] = i;
                    }
                    data.totalSubSteps++;
                });

                //Запускает подшаги по событию
                $('body').on('new-content.tour', function() {
                    if (tourStep.tips[data.curSubStep].waitNewContent || data.curSubStep == 0) {
                        setTimeout(function() {
                            showSubTip(true);
                        }, 50);
                    }
                });

                //Возвращаемся на предыдущий шаг, если закрывается окно диалога
                $('body').on('dialogBeforeDestroy.tour', function() {
                    goPrev();
                });

                var showSubTip = function(skipCheckWait) {

                    $('.tipForm').each(function() {
                        if (tourStep.tips[ $(this).data('substep') ].onStop) {
                            tourStep.tips[ $(this).data('substep') ].onStop();
                        }
                        $(this).remove();
                        clearTimeout(checkTimeout);
                    });

                    tip = tourStep.tips[data.curSubStep];

                    if (!tip) return;

                    //Устанавливаем значения по умолчанию
                    tip = $.extend({
                        tipText: '',
                        css: {},
                        animation: null,
                        correctionX: 0,
                        correctionY: 0,
                        onStart: null,
                        onStop: null
                    }, tip);

                    var element = $(tip.element).first();

                    if ( (!skipCheckWait && tip.waitNewContent) ) return;

                    //Проверяем условие для отображения
                    if (typeof(tip.ifTrue) == 'function' ) {
                        if (!tip.ifTrue()) {
                            //Если отображать tip не следует, то перекидываем на другой tip
                            data.curSubStep = (tip.elseStep !== undefined) ? tipMap[tip.elseStep] : data.curSubStep + 1;
                            showSubTip();
                            return;
                        }
                    }

                    var goToNextSubStep = function() {
                        data.curSubStep = (tip.next) ? tipMap[tip.next] : data.curSubStep + 1;
                        showSubTip();
                    }

                    if ( !element.length  ) {
                        //Пытаемся перейти на следующий элемент
                        if (data.curSubStep>0) goToNextSubStep();
                        return;
                    }

                    var oneTip = $('<div class="tipTour tipForm" />')
                    oneTip.html('<div class="tipContent">'+tip.tipText+'</div>')
                        .data('substep', data.curSubStep)
                        .append('<i class="corner"><!----></i>')
                        .append(getStatusLine())
                        .css(tip.css);

                    if (tip.correctionX) {
                        oneTip
                            .css('marginLeft', tip.correctionX);

                        if (tip.correctionX<0) {
                            oneTip.find('.corner').css({
                                left: -tip.correctionX
                            });
                        }
                    }

                    if (tip.correctionY) {
                        oneTip.css('marginTop', tip.correctionY);
                    }

                    if (tip.bottom) {
                        oneTip
                            .addClass('bottom')
                            .appendTo('body');

                        updateTipFormPosition(element, tip, oneTip);
                        $(window).on('resize.tour', function() {
                            updateTipFormPosition(element, tip, oneTip);
                        });

                    } else {
                        if (tip.insertAfter) {
                            oneTip
                                .insertAfter(element);
                        } else {
                            oneTip
                                .appendTo(element.parent());
                        }
                    }

                    if (tip.onStart) tip.onStart();

                    scrollWindow(oneTip);

                    if (tip.checkPattern) {
                        if ( (element.is('input') && element.attr('type') == 'text')
                            || element.is('textarea')) {

                            var checkText = function() {
                                if (tip.checkPattern.test( $(element).val() )) {
                                    goToNextSubStep();
                                } else {
                                    checkTimeout = setTimeout(checkText, 1500);
                                }
                            }
                            checkTimeout = setTimeout(checkText, 1500);
                        }
                        if (element.is('input') && element.attr('type') == 'checkbox') {
                            element.off('.tour').on('change.tour', function(e) {
                                if ($(this).is(':checked') ==  tip.checkPattern) {
                                    element.off('.tour');
                                    goToNextSubStep();
                                }
                            });
                        }

                        if (element.is('select')) {
                            element.off('.tour').on('change.tour', function(e) {
                                if (tip.checkPattern.test( $(this).val() )) {
                                    element.off('.tour');
                                    goToNextSubStep();
                                }
                            });
                        }
                    }

                    if (tip.checkSelectValue) {
                        element.on('change.tour', function(e) {
                            if (tip.checkSelectValue.test( $('option:selected', e.currentTarget).html() )) {
                                element.off('.tour');
                                goToNextSubStep();
                            }

                        });
                    }

                    if (tip.watch) {
                        var watchElement = tip.watch.element ? $(tip.watch.element) : element;

                        watchElement.one(tip.watch.event+'.tour', function() {
                            if (tip.watch.next) {
                                runAction(tip.watch.next);
                            } else {
                                goToNextSubStep();
                            }
                        });
                    }

                    if (tip.tinymceTextarea) {
                        var textarea = $(tip.tinymceTextarea);

                        var checkText = function() {
                            if (tip.checkPattern.test( textarea.html() )) {
                                goToNextSubStep();
                            } else {
                                setTimeout(checkText, 1000);
                            }
                        };
                        setTimeout(checkText, 1000);
                    }

                    if (tip.checkTimeout) {
                        checkTimeout = setTimeout(function() {
                            goToNextSubStep();
                        }, tip.checkTimeout);
                    }
                }

                showSubTip();
            },

            updateTipFormPosition = function(element, tipData, oneTip)
            {
                var position = {
                    top: element.offset().top + getHeight(element),
                    left: element.offset().left
                }

                if (tipData.bottom) {
                    position.top = element.offset().top - getHeight(oneTip);
                }

                if (oneTip.css('position') == 'fixed') {
                    position.top = position.top - $(window).scrollTop();
                }

                oneTip.css(position);

                //Выставляем смещение выноски
                if (tipData.correctionX) {
                    oneTip.find('.corner').css({
                        left: tipData.correctionX
                    });
                }
            },

            showInfo = function(step)
            {
                var tourStep = data.tour[step];
                overlayShow();

                if (tourStep.tips)
                    $.each(tourStep.tips, function(i, tip) {

                        //Устанавливаем значения по умолчанию
                        tip = $.extend({
                            tipText: '',
                            css: {},
                            animation: null,
                            position:['left', 'bottom'],
                            correctionX: 0,
                            correctionY: 0
                        }, tip);

                        var element = $(tip.element).first();

                        var canShow = element.length && (!tip.whenUse || tip.whenUse(element));

                        if (canShow) {
                            var oneTip = $('<div class="tipInfoTour" />')
                            oneTip.html('<div class="tipInfoTourContent">'+tip.tipText+'</div>')
                                .append('<i class="corner"><span class="line"><span class="arrow"></span></span></i>')
                                .addClass( tip.position[0]+tip.position[1][0].toUpperCase()+tip.position[1].substring(1) )
                                .css(tip.css)
                                .appendTo('body');

                            updateTipInfoPosition(tip.element, tip, oneTip);

                            $(window).on('resize.tour', function() {
                                updateTipInfoPosition(tip.element, tip, oneTip);
                            });

                            if (tip.animation) {
                                oneTip.addClass(tip.animation + ' animated');
                            }
                        }
                    });
                var
                    text = $('<div class="contentTour">').html(tourStep.message);

                $('<div class="infoTour" />')
                    .append('<div class="infoBack"/>')
                    .append('<h2>'+lang.t('Информация')+'</h2>')
                    .append(text)
                    .append(getStatusLine())
                    .appendTo('body')
                    .css('marginTop', -$('.infoTour').height()/2)
                    .draggable({handle: 'h2'});

                $('.goNext').addClass('pulse animated infinite');
            },

            getWidth = function(element) {
                return element.width() + parseInt(element.css('paddingLeft')) + parseInt(element.css('paddingRight'));
            },

            getHeight = function(element) {
                return element.height() + parseInt(element.css('paddingTop')) + parseInt(element.css('paddingBottom'));
            },

            updateTipInfoPosition = function(elementString, tipData, oneTip) {
                var
                    element = $(elementString),
                    horiz = tipData.position[0],
                    vert = tipData.position[1],
                    cornerSourceY,
                    css = {};

                if (!element.is(':visible')) {
                    oneTip.css('visibility', 'hidden');
                    return false;
                } else {
                    oneTip.css('visibility', 'visible');
                }

                switch(horiz) {
                    case 'left': css.left = element.offset().left + getWidth(element) - getWidth(oneTip);
                        if (vert == 'middle') {
                            css.left = css.left - getWidth(element);
                        }
                        break;
                    case 'center': css.left = element.offset().left + getWidth(element)/2 - getWidth(oneTip)/2; break;
                    case 'right': css.left = element.offset().left;
                        if (vert == 'middle') {
                            css.left = css.left + getWidth(element);
                        }
                        break;
                }

                switch(vert) {
                    case 'top': css.top = element.offset().top - getHeight(oneTip) - data.options.tipInfoCorrectionY; cornerSourceY = element.offset().top; break;
                    case 'middle': css.top = element.offset().top + getHeight(element)/2 - getHeight(oneTip)/2; cornerSourceY = element.offset().top + getHeight(element)/2; break;
                    case 'bottom': css.top = element.offset().top + getHeight(element) + data.options.tipInfoCorrectionY; cornerSourceY = element.offset().top + getHeight(element);  break;
                }

                css.marginTop = tipData.correctionY;
                css.marginLeft = tipData.correctionX;

                if (tipData.fixed) {
                    oneTip.css('position', 'fixed');
                }

                oneTip.css(css);

                //Устанавливаем высоту выноски
                var cornerCss = {
                    left: 'auto',
                    right: 'auto',
                    top: 'auto',
                    bottom: 'auto',
                    width: 10,
                    height: 1,
                }
                if (vert == 'middle') {

                    //Выноска горизонтальная
                    cornerCss.top = cornerSourceY-css.top;

                    if (horiz == 'right') {
                        cornerCss.width = (css.left + tipData.correctionX) - (element.offset().left + getWidth(element));
                        cornerCss.left = -cornerCss.width;
                    }
                    if (horiz == 'left') {
                        cornerCss.width = element.offset().left - (css.left + getWidth(oneTip) + tipData.correctionX);
                        cornerCss.right = -cornerCss.width;
                    }

                } else {
                    //Выноска вертикальная
                    cornerCss.left = element.offset().left + getWidth(element)/2 - css.left;
                    if (vert == 'bottom') {
                        cornerCss.height = Math.abs(cornerSourceY - css.top) + css.marginTop;
                        cornerCss.top = -cornerCss.height;
                    }
                    if (vert == 'top') {
                        cornerCss.height = Math.abs(cornerSourceY - (css.top + getHeight(oneTip))) - css.marginTop;
                        cornerCss.bottom = -cornerCss.height;
                    }
                }

                oneTip.find('.corner').css(cornerCss);
            },

            getStatusLine = function()
            {
                var
                    tourStep = data.tour[data.curStep],
                    curSubStep = '',
                    showNext = false;

                if (tourStep.type == 'form') {
                    var
                        curSubStep = '<span class="tourSubStep">.'+(data.curSubStep)+'</span>',
                        showNext = curSubStep < data.totalSubSteps;
                }

                var infoline = $('<div class="infoLineTour">').html(
                    '<span class="infoLineStep">'+lang.t('шаг')+' <strong>'+data.curStepIndex+'</strong>'+curSubStep+' '+lang.t('из')+' '+data.tourTotalSteps+'</span>'
                );

                if (data.curStepIndex>1) {
                    infoline.prepend( $('<a class="goPrev"><i class="zmdi zmdi-arrow-left"><!----></i><span>'+lang.t('назад')+'</span></a>').on('click', goPrev) );
                    $('body').on('keydown.tour', function(e) {
                        if (e.ctrlKey && e.keyCode == 37) goPrev();
                    });
                }
                if (data.curStepIndex < data.tourTotalSteps || showNext) {
                    infoline.append( $('<a class="goNext"><span>'+lang.t('далее')+'</span><i class="zmdi zmdi-arrow-right"><!----></i></a>').on('click', goNext) );
                    $('body').on('keydown.tour', function(e) {
                        if (e.ctrlKey && e.keyCode == 39) goNext();
                    });
                }

                infoline.append( $('<a class="tourClose zmdi zmdi-close"></a>').on('click', methods.stop) );

                return infoline;
            },

            hideStep = function()
            {
                overlayHide();
                hideDialog();
                $('body').off('dialogBeforeDestroy.tour');
                $('.infoTour, .tipTour, .tipInfoTour').remove();
                $(window).off('.tour');
                $('*').off('.tour');
                clearTimeout(timeoutHandler);

                if (data.curStep && typeof(data.tour[data.curStep].onStop) == 'function') data.tour[data.curStep].onStop();
            },

            hideDialog = function()
            {
                overlayHide();
                $('#tipDialog').remove();
            };

        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object') {
            return methods.init.apply( this, args );
        }
    };
})(jQuery);
/**
 * Файл с описанием схемы интерактивного тура по административной панели ReadyScript
 *
 * @author RedyScript lab.
 */
$(function() {

    var isCategoryExpanded = function(element) {
        return (!element.closest('.left-up').length
        && window.matchMedia('(min-width: 992px)').matches)
    };

    /**
    * Тур по первичной настройке сайта
    */
    var tourTopics = {
        'base': lang.t('Базовые настройки'),
        'products': lang.t('Категории и Товары'),
        'menu': lang.t('Меню'),
        'article': lang.t('Новости'),
        'delivery': lang.t('Способы доставки'),
        'payment': lang.t('Способы оплаты'),
        'debug': lang.t('Правка информации на сайте')
    };

    var welcomeTour = {};

    welcomeTour.commonStart =  {
        'index': {
                url: '/',
                topic: tourTopics.base,
                type: 'dialog',
                message: lang.t(`<div class="tourIndexWelcome">Рады приветствовать Вас!</div>
                    <div class="tourIndexBlock">
                        <div class="tourBorder"></div>
                        <p class="tourHello">Хотели бы Вы пройти<br> интерактивный курс обучения?</p>
                        <div class="tourLegend">
                            <a class="tourTop first indexTipToAdmin" data-step="index-tip-toadmin">Базовые настройки</a>
                            <a class="adminCatalogAddInfo" data-step="admin-catalog-add-info">Категории<br> и Товары</a>
                            <a class="tourTop menuCtrl" data-step="menu-ctrl">Текстовые<br> страницы<br> (Меню)</a>
                            <a class="articleCtrl" data-step="article-ctrl">Новости</a>`+
                            (global.scriptType != 'Shop.Base' ? `<a class="tourTop shopDeliveryCtrl" data-step="shop-deliveryctrl">Способы доставки</a>
                            <a class="shopPaymentCtrl" data-step="shop-paymentctrl">Способы оплаты</a>` : '') +
                            `<a class="tourTop debugIndex" data-step="debug-index">Правка информации на сайте</a>
                        </div>
                    </div>
                </div>`, null, 'tourWelcome'),
                buttons: {
                    yes: {
                        text: lang.t('Да, пройти курс с начала'),
                        step: 'index-tip-toadmin'
                    },
                    no: false
                }
            },
            
            'index-tip-toadmin': {
                url: '/',
                topic: tourTopics.base,            
                tip: {
                    element: '.header-panel .to-admin',
                    tipText: lang.t('Все настройки интернет-магазина располагаются в административной панели. Нажмите на кнопку быстрого перехода в панель администрирования.')
                }
            },
            
            'admin-index': {
                url: '%admin%/',
                topic: tourTopics.base,            
                type: 'info',
                message: lang.t('Это главный экран панели управления магазином. Здесь могут размещаться информационные виджеты с самой актуальной информацией по ключевым показателям магазина.'),
                tips: [
                    {
                        element: '.addwidget',
                        tipText: lang.t('Кнопка "Добавить виджет" откроет список имеющихся в системе виджетов'),
                        position: ['center', 'bottom'],  //Положение относительно element - [(left|center|right),(top|middle|bottom)]
                        fixed:true,
                        animation: 'bounceInDown'
                    },
                    {
                        element: '.action-zone .action.to-site',
                        tipText: lang.t('Быстрый переход на сайт'),
                        position: ['left', 'bottom'],
                        animation: 'slideInLeft'
                    },
                    {
                        element: '.action-zone .action.clean-cache',
                        tipText: lang.t('Кнопка для очистки кэша системы'),
                        position: ['left', 'bottom'],
                        correctionY: 50,
                        animation: 'slideInDown'
                    },
                    {
                        element: '.panel-menu .current',
                        tipText: lang.t('Показан текущий сайт. Если управление ведется несколькими сайтами, то при наведении будет показан список сайтов.'),
                        position: ['left', 'bottom'],
                        correctionY: 100,
                        css: {
                            width: 300
                        },
                        animation: 'bounceInDown'
                    }
                ]
            },
            
            'admin-index-to-siteoptions': {
                url: '%admin%/',
                topic: tourTopics.base,
                tip: {
                    element: ['a[data-url$="/menu-ctrl/"]', '#menu-trigger'],
                    tipText: lang.t('Перейдите в раздел <i>Веб-сайт &rarr; Настройка сайта</i>'),
                    correctionX: 40,
                    fixed: true
                },
                onStart: function() {
                    $('a[href$="/site-options/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('a[href$="/site-options/"]').removeClass('menuTipHover');
                }
            },
            
            'admin-siteoptions': {
                url: '%admin%/site-options/',
                topic: tourTopics.base,            
                type: 'info',
                message: lang.t('В этом разделе необходимо настроить основные параметры текущего сайта, к которым относятся: '+
                '<ul><li>контактные данные администратора магазина (будут использоваться для уведомлений обо всех событиях в интернет-магазине);</li>'+
                '<li>реквизиты организации продавца (будут использоваться для формирования документов покупателям);</li>'+
                '<li>логотип интернет-магазина;</li>'+
                '<li>тема оформления сайта;</li>'+
                '<li>параметры писем, отправляемых интернет-магазином.</li></ul>', null, 'tourAdminSiteOptions'),
                tips:[
                    {
                        element: '.tab-nav li:eq(3)',
                        tipText: lang.t('Заполните сведения во всех вкладках. При наведении мыши на символ вопроса, расположенный справа от поля, отобразится подсказка по нзначению и заполнению поля.'),
                        position: ['center', 'bottom'],
                        correctionX:50,
                        css: {
                            width:300
                        },
                        animation: 'slideInDown'
                    }
                ],
                buttons: {
                    next: 'admin-siteoptions-save'
                }
            },
            
            'admin-siteoptions-save': {
                url: '%admin%/site-options/',
                topic: tourTopics.base,            
                tip: {
                    element: '.btn.crud-form-apply',
                    tipText: lang.t('Заполните сведения во всех вкладках, расположенных выше. Далее, нажмите на зеленую кнопку, чтобы сохранить изменения.'),
                    correctionY: -15,
                    bottom: true,
                    css: {
                        position: 'fixed'
                    }
                },
                watch: {
                    element: '.btn.crud-form-apply',
                    event: 'click',
                    next:'admin-siteoptions-to-products'
                }
            },
            
            'admin-siteoptions-to-products': {
                url: '%admin%/site-options/',
                topic: tourTopics.base,
                tip: {
                    element: ['a[data-url$="/catalog-ctrl/"]', '#menu-trigger'],
                    tipText: lang.t('Теперь необходимо добавить товары, для этого перейдите в раздел <i>Товары &rarr; Каталог товаров</i>'),
                    correctionX: 40,
                    css: {
                        zIndex: 50
                    }
                },
                onStart: function() {
                    $('.side-menu ul a[href$="/catalog-ctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu ul a[href$="/catalog-ctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'admin-catalog-add-info': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                type: 'info',
                message: lang.t(`В этом разделе происходит управление товарами и категориями товаров. 
                            Обратите внимание на расположение кнопок создания объектов.
                            <p>На следующем шаге мы попробуем создать, для примера, одну категорию и один товар. 
                            По аналогии вы сможете наполнить каталог собственными категориями и товарами.`, null, 'tourAdminCatalogAddInfo'),
                tips: [
                    {
                        element: '.treehead .addspec',
                        tipText: lang.t('Создать спец.категорию <br>(например: новинки, лидеры продаж,...)'),
                        position:['left', 'bottom'],
                        whenUse: isCategoryExpanded,
                        animation: 'slideInLeft'
                    },
                    {
                        element: '.treehead .add',
                        tipText: lang.t('Создать категорию товаров'),
                        whenUse: isCategoryExpanded,
                        position:['left', 'bottom'],
                        correctionY:60,
                        animation: 'slideInDown'
                    },
                    {
                        element: '.c-head .btn-group:contains("'+lang.t("добавить товар")+'")',
                        tipText: lang.t('Создать товар'),
                        position:['left', 'middle'],
                        correctionX:-30,
                        animation: 'fadeInLeft'
                    },
                    {
                        element: '.c-head .btn-group:contains("'+lang.t("Импорт/Экспорт")+'")',
                        tipText: lang.t('Через эти инструменты можно массово загрузить товары, <br>категории в систему через CSV файлы. Подробности в <a target="_blank" href="http://readyscript.ru/manual/catalog_csv_import_export.html">документации</a>.'),
                        animation: 'slideInDown'
                        
                    }
                ],
                buttons: {
                    next: 'admin-siteoptions-save'
                }
            },
            
            'admin-catalog-add-dir': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                tip: {
                    element: [
                        {
                            selector: '.treehead .add',
                            whenUse: isCategoryExpanded
                        },
                        {
                            selector: '.c-head .btn.btn-success',
                            left: true,
                            correctionX:-20
                        }],
                    tipText: lang.t('Перед добавлением товара нужно создать его категорию. Для примера, создадим тестовую категорию "<b>Холодильники</b>". Нажмите на кнопку <i>создать категорию</i> или найдите это действие в выпадающем списке зеленой кнопки вверху-справа.'),
                },
                watch: {
                    element: '.treehead .add, .c-head a:contains("добавить категорию")',
                    event: 'click',
                    next: 'admin-catalog-add-dir-form'
                }
            },
            
            //Шаги, связанные с добавлением категории
            
            'admin-catalog-add-dir-form': {
                url: '%admin%/catalog-ctrl/?pid=0&do=treeAdd',
                topic: tourTopics.products,
                type: 'form',
                tips: [
                    {
                        element: '.crud-form [name="name"]',
                        tipText: lang.t('Укажите название - <b>Холодильники</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="alias"]',
                        tipText: lang.t('Укажите Псевдоним - это имя на английском языке, которое будет использоваться для построения URL-адреса страницы'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.formbox .tab-nav',
                        tipText: lang.t(`Перейдите на вкладку <i>Характеристики</i>. Для примера создадим 1 характеристику (мощность), <br>
                                  которая обязательно будет присутствовать у всех товаров создаваемой категории.`),
                        insertAfter:true,
                        correctionX:100,
                        watch: {
                            element: '.formbox .tab-nav li > a:contains("'+lang.t('Характеристики')+'")',
                            event: 'click'
                        }
                    },
                    {
                        element: '.property-actions .add-property',
                        tipText: lang.t('Нажмите добавить характеристику'),
                        watch: {
                            event: 'click',
                        },
                        onStart: function() {
                            $('.frame[data-name="tab2"]').append('<div style="height:110px" id="tourPlaceholder1"></div>');
                        }
                    },
                    {
                        element: '.property-form .p-title',
                        tipText: lang.t('Укажите название - <b>Мощность</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.property-form .p-type',
                        tipText: lang.t('Укажите тип - <b>Список</b>, чтобы в дальнейшем включить фильтр по данной харктеристике'),
                        checkPattern: /^(list)$/gi
                    },
                    {
                        element: '.property-form .p-unit',
                        tipText: lang.t('Укажите единицу измерения - <b>Вт</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.property-form .p-new-value',
                        tipText: lang.t('Укажите возможное значение мощности - <b>1000</b> и нажмите справа <b>добавить</b>'),
                        watch: {
                            element: '.p-add-new-value',
                            event: 'click'
                        }
                    },
                    {
                        element: '.property-form .p-new-value',
                        tipText: lang.t('Укажите еще одно возможное значение мощности - <b>2000</b> и нажмите справа <b>добавить</b>'),
                        watch: {
                            element: '.p-add-new-value',
                            event: 'click'
                        }
                    },
                    {
                        element: '.property-form .add',
                        tipText: lang.t('<i>Добавьте</i> характеристику к категории'),
                        css: {
                            marginTop:46
                        },
                        watch: {
                            event: 'click',
                        }
                    },
                    {
                        waitNewContent: true,
                        element: '.property-container .property-item .h-public',
                        tipText: lang.t('Установите флажок <i>Отображать в поиске на сайте</i>, чтобы по данной характеристике можно было отфильтровать товары на сайте. Подробности в <a href="http://readyscript.ru/manual/catalog_categories.html#cat_tab_characteristics" target="_blank">документации</a>.'),
                        checkPattern: true,
                        correctionX: -230,
                        css: {
                            width:300
                        }
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t('Нажмите на кнопку <i>Сохранить</i>, чтобы создать категорию'),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'admin-catalog-add-product'
                        }
                    }
                ]
            },        
            
            'admin-catalog-add-product': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                tip: {
                    element: '.c-head .btn.btn-success:contains("'+lang.t('добавить товар')+'")',
                    tipText: lang.t('Чтобы добавить товар, нажмите на зеленую кнопку <i>Добавить товар</i>'),
                },
                watch: {
                    element: '.c-head .btn.btn-success:contains("'+lang.t('добавить товар')+'")',
                    event: 'click',
                    next: 'admin-catalog-add-product-form'
                }
            },
            
            'admin-catalog-add-product-form': {
                url: '%admin%/catalog-ctrl/?dir=0&do=add',
                topic: tourTopics.products,
                type: 'form',
                tips: [
                    {
                        element: '.crud-form [name="title"]',
                        tipText: lang.t('Укажите название товара - <b>Холодильник ТОМАС</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="alias"]',
                        tipText: lang.t('Укажите любое URL имя на англ.языке. <br>Будет использовано для создания адреса страницы товара'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '#tinymce-description_parent',
                        tinymceTextarea: '#tinymce-description',
                        tipText: lang.t('Укажите описание товара'),
                        bottom:true,
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="barcode"]',
                        tipText: lang.t('Укажите артикул товара'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name^="excost"]:first',
                        tipText: lang.t('Укажите стоимость товара'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name^="xdir[]"]',
                        tipText: lang.t('Выберите категорию - <b>Холодильники</b>'),
                        checkSelectValue: /^.*$/gi,
                        correctionX:150,
                    },
                    {
                        element: '.formbox .tab-nav',
                        tipText: lang.t('Теперь добавим характеристику товару, для этого перейдите на вкладку <i>Характеристики</i>'),
                        insertAfter:true,
                        correctionX:100,
                        watch: {
                            element: '.formbox .tab-nav li > a:contains('+lang.t('Характеристики')+')',
                            event: 'click',
                        }
                    },
                    {
                        ifTrue: function() {
                            return !$('.item-title:contains("' + lang.t('Мощность') + '")').length>0;
                        },
                        elseStep: 'myval_noajax',
                        element: '.property-actions .add-property',
                        tipText: lang.t('Нажмите <i>Добавить характеристику</i>'),
                        watch: {
                            event: 'click',
                        }
                    },
                    {
                        element: '.property-form .p-proplist',
                        tipText: lang.t('Выберите характеристику - <b>Мощность</b>'),
                        checkPattern: /^\d+$/gi
                    },
                    
                    {
                        element: '.property-form .add',
                        tipText: lang.t('<i>Добавьте</i> характеристику к товару'),
                        css: {
                            marginTop:46
                        },
                        watch: {
                            event: 'click',
                        }
                    },
                    {
                        label: 'myval_ajax',
                        waitNewContent: true,
                        ifTrue: function() {
                            //Если есть флажок - "задать персональное значение"
                            return $(lang.t('.property-item:contains("Мощность") .h-useval')).length>0;
                        },
                        element: lang.t('.property-item:contains("Мощность") .h-useval'),
                        tipText: lang.t('Отметьте флажок, чтобы задать индивидуальное значение характеристики для товара'),
                        checkPattern: true,
                        next: 'propval'
                    },
                                    
                    {
                        label: 'myval_noajax',
                        ifTrue: function() {
                            //Если есть флажок - "задать персональное значение"
                            return $('.property-item:contains("Мощность") .h-useval').length>0;
                        },
                        element: '.property-item:contains("Мощность") .h-useval',
                        tipText: lang.t('Отметьте флажок, чтобы задать индивидуальное значение характеристики для товара'),
                        checkPattern: true
                    },
                    {
                        label: 'propval',
                        element: '.property-item:contains("Мощность") .inline-item:contains("1000") input',
                        tipText: lang.t('Укажите, что мощность холодильника - 1000 Вт'),
                        checkPattern: true
                    },
                    {
                        element: '.formbox .tab-nav',
                        tipText: lang.t('На закладке <i>Комплектации</i> можно задать остатки, а также <a href="http://readyscript.ru/manual/catalog_products.html#catalog_products_tab_offers">вариации(комплектации)</a> товара.'),
                        insertAfter:true,
                        correctionX:100,
                        watch: {
                            element: '.formbox .tab-nav li > a:contains('+lang.t("Комплектации")+')',
                            event: 'click',
                        }
                    },
                    {
                        element: '.crud-form [name^="offers[main][stock_num]"]:first',
                        tipText: lang.t('Укажите остаток товара на всех складах - <i>10</i>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.formbox .tab-nav',
                        tipText: lang.t('Загрузите фотографии на вкладке <i>Фото</i>'),
                        insertAfter:true,
                        correctionX:100,
                        watch: {
                            element: '.formbox .tab-nav li > a:contains(' + lang.t("Фото") + ')',
                            event: 'click',
                        }
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t('При желании вы можете заполнить сведения на оставшихся вкладках товара.<br> Затем нажмите на кнопку <i>Сохранить</i>, чтобы создать товар'),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'admin-catalog'
                        },
                        
                    }
                ],
            },
            
            'admin-catalog': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                type: 'info',
                message: lang.t(`Товар и категория добавлены. 
                          В дальнейшем Вы часто будете пользоваться данным разделом, чтобы корректировать описания товаров, цены, количество товаров, и т.д. 
                          Предлагаем ознакомиться с основными элементами управления, присутствующими на данной странице.`),
                tips: [
                    {
                        element: '.rs-table .options',
                        tipText: lang.t('Настройка состава колонок <br>таблицы и сортировки по-умолчанию'),
                        animation: 'slideInDown'
                    },
                    {
                        element: '.rs-table thead th:eq(4)',
                        tipText: lang.t('При нажатии на заголовок колонки <br>можно изменять сортировку данных в таблице'),
                        correctionY:70,
                        correctionX:40,
                        animation: 'slideInDown'
                    },
                    {
                        element: '.right-column .bottom-toolbar .crud-multiedit',
                        tipText: lang.t('В нижней панели представлены действия (редактировать, удалить), <br>которые можно применить ко всем <br>отмеченным элементам (товарам или категориям).'),
                        position: ['right', 'top'],
                        animation: 'bounceInDown',
                        css: {
                            position:'fixed'
                        }
                    },
                    {
                        element: '.treehead .showchilds-on, .showchilds-off',
                        tipText: lang.t('Включить/выключить показ товаров из вложенных категорий'),
                        whenUse:isCategoryExpanded,
                        position:['right', 'top'],
                        correctionY:-20,
                        animation: 'rotateIn'
                    },                
                    {
                        element: '.rs-table .chk',
                        tipText: lang.t('Можно отметить товары как на одной,<br> так и на всех страницах'),
                        position:['right', 'bottom'],
                        animation: 'slideInLeft'
                    },
                    {
                        element: '.treebody > li:eq(1) .move',
                        tipText: lang.t('Сортируйте категории с помощью перетаскивания'),
                        whenUse:isCategoryExpanded,
                        position:['right', 'bottom'],
                        animation: 'slideInDown'
                    }
                ],
                onStart: function() {
                    var act = function() {
                        $('.rs-table .chk').addClass('chk-over');
                        $('.treebody > li:eq(3)').addClass('over');
                        $('.treebody > li:eq(1)').addClass('drag');
                        $('.rs-table tbody tr:eq(7)').addClass('over');
                    }
                    
                    $('body').on('new-content.tour', act);
                    act();
                },
                onStop: function() {
                    $('.rs-table .chk').removeClass('chk-over');
                    $('.treebody > li:eq(3)').removeClass('over');
                    $('.treebody > li:eq(1)').removeClass('drag');
                    $('.rs-table tbody tr:eq(7)').removeClass('over');
                }
            },
            
            'to-menu-ctrl': {
                url: '%admin%/catalog-ctrl/',
                topic: tourTopics.products,
                tip: {
                    element: 'a[data-url$="/menu-ctrl/"]',
                    tipText: lang.t('Перейдите в раздел <i>Веб-сайт &rarr; Меню</i>'),
                    correctionX: 40,
                    css: {
                        zIndex:50
                    }
                },
                onStart: function() {
                    $('.side-menu ul a[href$="/menu-ctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu ul a[href$="/menu-ctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'menu-ctrl': {
                url: '%admin%/menu-ctrl/',
                topic: tourTopics.menu,
                type: 'info',
                message: lang.t(`В данном разделе можно создавать иерархию страниц сайта разных типов, которые могут быть доступны пользователям через меню. Каждой странице будет присвоен определенный URL адрес, по которому она будет доступна из браузера. 
                         <p>Например, если вы желаете: <ul>
                         <li>создать страницу с какой-либо текстовой информацией, то необходимо создать пункт меню с типом "<b>Статья</b>".</li>
                         <li>создать страницу, на которой должны быть представлены функциональные блоки с каким-либо более сложным поведением (например, форма обратной связи), то необходимо создать пункт меню с типом "<b>Страница</b>". 
                         Далее эту страницу можно будет настроить в разделе Веб-сайт &rarr; Конструктор сайта.</li>
                         <li>создать простую ссылку в меню, то используйте тип "<b>Ссылка</b>" для такого пункта меню.</li>
                         </ul><p>Ознакомьтесь с основными функциональными кнопками на данной странице. 
                         На следующем шаге, мы создадим для примера пункт меню с информацией о рекламной акции в интернет-магазине.`, null, 'tourMenuCtrlInfo'),
                tips: [
                    {
                        element: '.c-head .btn:contains("' + lang.t('добавить пункт меню') + '")',
                        tipText: lang.t('Создать новый пункт меню'),
                        animation: 'bounceInDown'
                    },
                    {
                        element: '.c-head .btn:contains("' + lang.t('Импорт/Экспорт') + '")',
                        tipText: lang.t('Через эти инструменты можно массово <br>загрузить пункты меню в систему через CSV файлы.'),
                        animation: 'slideInDown',
                        correctionY:60
                    },
                    {
                        element: '.activetree  .allplus',
                        tipText: lang.t('Развернуть отображение дерева пунктов меню'),
                        position:['right', 'bottom'],
                        animation: 'slideInLeft'
                        
                    },
                    {
                        element: '.activetree  .allminus',
                        tipText: lang.t('Свернуть отображение дерева пунктов меню'),
                        position:['right', 'middle'],
                        correctionX:40,
                        animation: 'slideInDown'
                    }
                ]
            },
            
            'menu-ctrl-add': {
                url: '%admin%/menu-ctrl/',
                topic: tourTopics.menu,
                tip: {
                    element: '.c-head .btn:contains("' + lang.t("добавить пункт меню") + '")',
                    tipText: lang.t(`Добавим на сайте раздел <b>Акция</b>, в котором будет представлена текстовая информация. 
                              Нажмите на кнопку <i>Добавить пункт меню</i>`)
                },
                watch: {
                    element: '.c-head .btn:contains("' + lang.t("добавить пункт меню") + '")',
                    event: 'click',
                    next: 'menu-ctrl-add-form'
                }
            },
            
            'menu-ctrl-add-form': {
                url: '%admin%/menu-ctrl/?do=add',
                topic: tourTopics.menu,
                type: 'form',
                tips: [
                    {
                        element: '.crud-form [name="title"]',
                        tipText: lang.t('Укажите название пункта меню - <b>Акция</b>'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="alias"]',
                        tipText: lang.t('Укажите любое название пункта меню на Англ. языке. <br>Оно будет использоваться для построения URL адреса раздела.'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form #tinymce-content_ifr',
                        tinymceTextarea: '#tinymce-content',
                        tipText: lang.t('Укажите информацию об акции'),
                        bottom:true,
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.mce-ico.mce-i-image',
                        tipText: lang.t('Используйте кнопку с лупой, чтобы добавить изображения к тексту'),
                        correctionY:10,
                        correctionX:-50,
                        checkTimeout: 5000
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t(`После ввода всей необходимой текстовой информации, нажмите 
                                 <br>на кнопку <i>Сохранить</i>, чтобы создать раздел на сайте, который отобразится в меню`),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'to-article-ctrl'
                        },
                        
                    }
                ]
            },
            
            'to-article-ctrl': {
                url: '%admin%/menu-ctrl/',
                topic: tourTopics.article,
                tip: {
                    element: '.side-menu a:contains("'+lang.t('Веб-сайт')+'")',
                    tipText: lang.t('Перейдите в раздел <i>Веб-сайт &rarr; Контент</i>'),
                    correctionX: 50,
                    css: {
                        zIndex: 50
                    }
                },
                onStart: function() {
                    $('.side-menu ul a[href$="/article-ctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu ul a[href$="/article-ctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'article-ctrl': {
                url: '%admin%/article-ctrl/',
                topic: tourTopics.article,
                type: 'info',
                message: lang.t(`На этой странице происходит управление списками текстовых материалов, например новостями.
                         <p>Для добавления новости на сайте, достаточно создать статью в соответствующей категории.
                         <p>Также в этом разделе административной панели могут размещаться статьи, используемые темой оформления на различных страницах.`),
                tips: [
                    {
                        element: '.treehead .add',
                        tipText: lang.t('Создать категорию статей'),
                        position:['right', 'top'],
                        whenUse: isCategoryExpanded,
                        animation: 'slideInDown'
                    },
                    {
                        element: '.c-head .btn:contains("' + lang.t('добавить статью') + '")',
                        tipText: lang.t('Создать статью'),
                        animation: 'slideInDown'
                    },
                    {
                        element: '.treebody > li:eq(0)',
                        tipText: lang.t('Категория статей'),
                        whenUse: isCategoryExpanded,
                        position:['right', 'middle'],
                        animation: 'slideInLeft',
                        correctionX:40
                    }
                ]
            }
    }

    welcomeTour.commonEnd = {
            'to-index': {
                url: global.scriptType != 'Shop.Base' ? '%admin%/shop-paymentctrl/' : '%admin%/article-ctrl/',
                topic: tourTopics.payment,
                tip: {
                    element: '.header-panel .to-site',
                    tipText: lang.t('Основные настройки в административной панели произведены. Желаете добавлять товары, категории, новости, и т.д., не заходя в панель администрирования? Нажмите на кнопку <i>Перейти на сайт</i>, чтобы узнать как.')
                },
                watch: {
                    element: '.header-panel .to-site',
                    event: 'click',
                    next: 'debug-index'
                }
            },
            
            'debug-index': {
                url: '/',
                topic: tourTopics.debug,
                tip: {
                    element: '.debug-mode-switcher .rs-switch',
                    tipText: lang.t('Включите режим отладки, чтобы редактировать элементы прямо на странице'),
                    correctionY:40
                },
                whileTrue: function() {
                    return $('.debug-mode-switcher .rs-switch:not(.on)').length;
                }            
            },
            
            'debug-text': {
                url: '/',
                topic: tourTopics.debug,
                tip: {
                    element: '.module-wrapper:has([data-debug-contextmenu]):first',
                    tipText: lang.t('Любой товар, категорию, пункт меню, и т.д. на данной странице можно отредактировать, удалить или создать, кликнув над ним правой кнопкой мыши и выбрав необходимое действие.'),
                    correctionY:10,
                    css: {
                        zIndex:3
                    },
                    notFound: 'finish'
                },
                watch: {
                    element: '',
                    event: 'showContextMenu',
                    next: 'debug-block-text'
                },
                checkTimeout: 15000
            },
            
            'debug-block-text': {
                url: '/',
                topic: tourTopics.debug,
                tip: {
                    element: '.module-wrapper:eq(0) .debug-icon-blockoptions',
                    tipText: lang.t('Любой блок можно настроить, нажав на иконку с изображением гаечного ключа.'),
                    correctionY:10,
                    notFound: 'finish',
                    css: {
                        zIndex:3
                    }
                },
                onStart: function() {
                    $('.module-wrapper:eq(0)').addClass('over');
                },                
                onStop:  function() {
                    $('.module-wrapper:eq(0)').removeClass('over');
                },      
                watch: {
                    element: '.debug-icon-blockoptions',
                    event: 'click',
                    next: 'finish'
                },
                checkTimeout: 15000
            },
            
            'finish': {
                url: '/',
                topic: tourTopics.debug,
                type:'dialog',
                message: lang.t('<span class="finishText">Интерактивный курс по базовым настройкам<br> интернет-магазина успешно завершен.</span> <br>Более подробную информацию по возможностям платформы ReadyScript можно найти в <a href="http://readyscript.ru/manual/" target="_blank"><u>документации</u></a>.'),
                buttons: {
                    finish: {
                        text: lang.t('Закрыть окно'),
                        step: false                        
                    },
                    docs: {
                        text: lang.t('Документация'),
                        attr: {
                            href: 'http://readyscript.ru/manual/',
                            target: '_blank'
                        },
                        step:false
                    }
                }
            }
    }

    welcomeTour.shop = {
        'to-shop-deliveryctrl': {
                url: '%admin%/article-ctrl/',
                topic: tourTopics.article,            
                tip: {
                    element: '.side-menu > li > a[data-url$="/shop-orderctrl/"]',
                    tipText: lang.t('Теперь перейдем к настройке параметров, связанных с заказами. Перейдите в раздел <i>Магазин &rarr; Доставка &rarr; Способы доставки</i>'),
                    position:['right', 'top'],
                    bottom:true,
                    css: {
                        zIndex:50
                    }
                },
                onStart: function() {
                    $('.side-menu a[href$="/shop-regionctrl/"]:first').addClass('menuTipHover');
                    $('.side-menu a[href$="/shop-deliveryctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu a[href$="/shop-regionctrl/"]:first').removeClass('menuTipHover');
                    $('.side-menu a[href$="/shop-deliveryctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'shop-deliveryctrl': {
                url: '%admin%/shop-deliveryctrl/',
                topic: tourTopics.delivery,
                type:'info',
                message: lang.t(`В этом разделе необходимо произвести настройку способов доставок, которые будут
                         предложены пользователю во время оформления заказа. 
                         <p>До настройки данного раздела, необходимо иметь представление о том, как вы будете доставлять товары вашим покупателям и по каким ценам.
                         <p>Ознакомьтесь с основными инструментами представленными на данной странице.
                         <p>На следующем шаге, создадим для примера, "доставку по городу", которая будет стоить 500 руб.`, null, 'tourShopDeliveryCtrlInfo'),
                tips: [
                    {
                        element: '.c-head .btn.btn-success',
                        tipText: lang.t('Добавить способ доставки'),
                        position:['center', 'top'],
                        animation: 'slideInLeft'
                    },
                    {
                        element: '.c-head .btn:contains("' + lang.t('Импорт/Экспорт') + '")',
                        tipText: lang.t('Через эти инструменты можно массово загрузить способы доставок через CSV файлы.'),
                        animation: 'slideInDown',
                        correctionY:60
                    },
                    {
                        element: '.rs-table .sortdot',
                        tipText: lang.t('Сортировать способы доставок можно с помощью перетаскивания'),
                        position: ['right', 'top'],
                        animation: 'slideInLeft'
                    }
                ]
            },
            
            'shop-deliveryctrl-add': {
                url: '%admin%/shop-deliveryctrl/',
                topic: tourTopics.delivery,
                tip: {
                    element: '.c-head .btn.btn-success',
                    tipText: lang.t('Добавим, для примера, способ доставки <b>по городу</b>. Нажмите на кнопку <i>Добавить способ доставки</i>')
                },
                watch: {
                    element: '.c-head .btn.btn-success',
                    event: 'click',
                    next: 'shop-deliveryctrl-add-form'
                }            
            },
            
            'shop-deliveryctrl-add-form': {
                url: '%admin%/shop-deliveryctrl/?do=add',
                topic: tourTopics.delivery,
                type: 'form',
                tips: [
                    {
                        element: '.crud-form [name="title"]',
                        tipText: lang.t('Укажите название доставки - <b>по городу</b>. Будет отображено во время оформления заказа в списке возможных способов доставки.'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="description"]',
                        tipText: lang.t('Укажите условия или подробности доставки, которые будут отображаться под названием'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="xzone[]"]',
                        tipText: lang.t('Выберите географические зоны или пункт <b>- все -</b>, <br>чтобы определить регионы пользователей, для которых <br>будет отображен данный способ доставки'),
                        checkPattern: /^(0)$/gi
                    },
                    {
                        element: '.crud-form [name="user_type"]',
                        tipText: lang.t('Выберите категорию пользователей, <br>для которых будет доступна доставка.'),
                        watch: {
                            event: 'click'
                        }
                    },
                    {
                        element: '.crud-form [name="class"]',
                        tipText: lang.t(`Расчетный класс отвечает за то, какой модуль <br>будет расчитывать стоимость и обрабатывать доставку. 
                                  Выберите <b>Фиксированная цена</b>. Подробнее о других расчетных классах можно узнать <a href="http://readyscript.ru/manual/shop_delivery.html#shop_delivery_add" target="_blank">в документации</a>`),
                        watch: {
                            event: 'click'
                        }
                    },
                    {
                        element: '.crud-form [name="data[cost]"]',
                        tipText: lang.t('Укажите стоимость доставки по городу'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t(`После ввода всех необходимых параметров доставки, нажмите 
                                 <br>на кнопку <i>Сохранить</i>`),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'to-shop-paymentctrl'
                        },
                        
                    }
                ]
            },
            
            'to-shop-paymentctrl': {
                url: '%admin%/shop-deliveryctrl/',
                topic: tourTopics.delivery,
                tip: {
                    element: '.side-menu > li > a[data-url$="/shop-orderctrl/"]',
                    tipText: lang.t('Перейдите в раздел <i>Магазин &rarr; Способы оплаты</i>'),
                    bottom:true,
                    css: {
                        zIndex:50
                    }
                },
                onStart: function() {
                    $('.side-menu a[href$="/shop-paymentctrl/"]').addClass('menuTipHover');
                },
                onStop: function() {
                    $('.side-menu a[href$="/shop-paymentctrl/"]').removeClass('menuTipHover');
                }
            },
            
            'shop-paymentctrl': {
                url: '%admin%/shop-paymentctrl/',
                topic: tourTopics.payment,
                type: 'info',
                message: lang.t(`Перед началом продаж следует настроить способы оплат, которые будут предложены пользователю во время оформления заказа.
                          <p>Если Вы желаете добавить возможность оплачивать заказы с помощью электроных денег или карт Visa, Mastercard, и т.д., то
                            Вам необходимо предварительно создать аккаунт магазина на одном из сервисов-агрегаторов платежей - Yandex.Касса, Robokassa, PayPal, ...
                          <p>На следующем шаге, добавим для примера, способ оплаты "Безналичный расчет". Это будет означать, что покупатель сможет получить счет сразу после оформления заказа.`, null, 'tourShopPaymentCtrlInfo'),
                tips: [
                    {
                        element: '.c-head .btn.btn-success',
                        tipText: lang.t('Добавить способ оплаты'),
                        position:['center', 'top'],
                        animation: 'slideInDown'
                    },
                    {
                        element: '.rs-table .sortdot',
                        tipText: lang.t('Сортировать способы оплаты можно с помощью перетаскивания'),
                        position: ['right', 'top'],
                        animation: 'slideInLeft'
                    }
                ]
            },
            
            'shop-paymentctrl-add': {
                url: '%admin%/shop-paymentctrl/',
                topic: tourTopics.payment,
                tip: {
                    element: '.c-head .btn.btn-success',
                    tipText: lang.t('Добавим, для примера, способ оплаты <b>Безналичный расчет</b>. Нажмите на кнопку <i>Добавить способ оплаты</i>')
                },
                watch: {
                    element: '.c-head .btn.btn-success',
                    event: 'click',
                    next: 'shop-paymentctrl-add-form'
                }  
            },
            
            'shop-paymentctrl-add-form': {
                url: '%admin%/shop-paymentctrl/?do=add',
                topic: tourTopics.payment,
                type:'form',
                tips: [
                    {
                        element: '.crud-form [name="title"]',
                        tipText: lang.t('Укажите название способа оплаты - <b>Безналичный расчет</b>. Будет отображено во время оформления заказа в списке возможных способов оплаты.'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="description"]',
                        tipText: lang.t('Укажите условия или подробности оплаты. Будут отображены под названием'),
                        checkPattern: /^(.+)$/gi
                    },
                    {
                        element: '.crud-form [name="first_status"]',
                        tipText: lang.t('Счет будет доступен пользователю только если заказ находится в статусе <i>Ожидает оплату</i>, поэтому выберите стартовый статус <b>Ожидает оплату</b>'),
                        checkSelectValue: /^(Ожидает оплату)$/gi
                    },
                    {
                        element: '.crud-form [name="class"]',
                        tipText: lang.t('Расчетный класс отвечает за то, какой модуль будет обрабатывать платежи <br>или предоставлять документы на оплату пользователю. Выберите <b>Безналичный расчет</b>'),
                        checkPattern: /^(bill)$/gi
                    },
                    {
                        waitNewContent: true,
                        element: '.crud-form [name="data[use_site_company]"]',
                        tipText: lang.t('Установите флажок, чтобы использовать реквизиты, которые были заполнены раннее в разделе <i>Веб-сайт &rarr; Настройка сайта</i>.'),
                        checkPattern: true
                    },
                    {
                        element: '.bottom-toolbar .crud-form-save',
                        tipText: lang.t('После ввода всех необходимых параметров оплаты, нажмите <br>на кнопку <i>Сохранить</i>'),
                        bottom: true,
                        css: {
                            position: 'fixed'
                        },
                        correctionY: -20,
                        watch: {
                            element: 'body',
                            event: 'crudSaveSuccess',
                            next: 'to-index'
                        },
                    }
                    
                ]
            }
     };

    var tours = 
    {
        'welcome': $.extend({}, 
                            welcomeTour.commonStart, 
                            global.scriptType != 'Shop.Base' ? welcomeTour.shop : {},
                            welcomeTour.commonEnd)
    };
    
    $.tour(tours, {
        baseUrl: global.folder+'/',
        folder: global.folder,
        adminSection: global.adminSection
    });
    
});
/**
 * Plugin, активирующий отображение новостей в боковой панели
 */
$.widget("rs.newsShow", {
    options: {
        elementTitle: '.side-news__title',
        elementItem: '.side-news__item',
        elementAllViewed: '.all-viewed',
        elementNewClass: 'new',
        elementDisabledClass: 'disabled',
        elementViewCircle: '.view-circle'
    },
    _create: function() {
        var _this = this;
        this.element.on('click', function() {
            _this.open();
        });
    },

    /**
     * Открывает SideBar со списком новостей
     */
    open: function() {
        if ($.rs.loading.inProgress) return;

        var _this = this;
        this.panel = $('<div>').sidePanel({
            position: 'right',
            ajaxQuery: {
                url: this.element.data('urls').newsList
            },
            onLoad: function(e, data) {
                $(data.element)
                    .on('click', _this.options.elementItem, function(e) {
                        return _this._markAsViewed(e);
                    });
            },
            onShow: function(e, data) {
                $(data.panel)
                    .on('click', _this.options.elementAllViewed, function(e) {
                            return _this._markAllAsViewed(e, data.panel);
                    });
            }
        });
    },

    /**
     * Сообщает серверу о прочтении новости
     *
     * @private
     */
    _markAsViewed: function(e) {
        var item = $(e.target).closest(this.options.elementItem);
        if (item.hasClass(this.options.elementNewClass)) {
            $.ajaxQuery({
                url: this.element.data('urls').markAsViewed,
                data: {
                    id: $(e.target).closest(this.options.elementItem).data('id')
                },
                success: function (response) {
                    if (response.success) {
                        $.rsMeters('update', response.meters);
                    }
                }
            });
            this._setItemViewed( item );
        }
    },

    /**
     * Помечает все новости как просмотренные
     *
     * @param e
     * @private
     */
    _markAllAsViewed: function(e, panel) {
        if (!$(e.target).hasClass(this.options.elementDisabledClass)) {
            $.ajaxQuery({
                url: this.element.data('urls').markAllAsViewed,
                success: function (response) {
                    if (response.success) {
                        $.rsMeters('update', response.meters);
                    }
                }
            });

            this._setItemViewed( $(this.options.elementItem, panel) );
            $(this.options.elementAllViewed, panel).addClass(this.options.elementDisabledClass);
        }
    },

    _setItemViewed: function(item) {
        item.removeClass(this.options.elementNewClass)
            .find(this.options.elementViewCircle)
            .attr('data-original-title', lang.t('Прочитано'));
    }

});

$(function() {
    $('.rs-news-show').newsShow();
});
(function($){

    var elems = $([]),
        defaults = {
            timeToTriggered            : 100,
            charsCountToTriggered      : 8,
        };

    $.event.special.barcodescanned = {
        setup: function(data){
            $(this)
                .data( 'barcodescanned', {
                    chars: [],
                    settings: $.extend({}, defaults, data)
                })
                .bind( 'keypress', keypress_handler );
        },
        teardown: function(){
            $(this)
                .removeData( 'barcodescanned' )
                .unbind( 'keypress', keypress_handler );
        }
    };

    function keypress_handler( event ) {

        var elem = $(event.target),
            data = $('body').data( 'barcodescanned' );
        var time_to_triggered               = data.settings.timeToTriggered,
            chars_count_to_triggered        = data.settings.charsCountToTriggered,
            key_code                        = (event.which) ? event.which : event.keyCode;

        if (elem.is('input') || elem.is('textarea')) {
            return;
        }
        if ((key_code >= 65 && key_code <= 90) ||
            (key_code >= 97 && key_code <= 122) ||
            (key_code >= 48 && key_code <= 57) || key_code == 13
        ) {
            data.chars.push(String.fromCharCode(key_code));
        }

        setTimeout(function() {
            if (data.chars.length >= chars_count_to_triggered) {
                    let sku_val = data.chars.join('');
                    $('body').triggerHandler( 'barcodescanned', sku = sku_val);
            }
            data.chars = [];

        }, time_to_triggered);
    }

})(jQuery);

$(function () {
    $('body').on('barcodescanned', function(event, sku) {
        var input = $('.ui-dialog:last input[data-sku]:first');
        if (!input.length) {
            input = $('[data-sku]:first');
        }

        if (input.length) {
            input.val(sku);
            if (input.hasClass('submit')) {
                input.closest('form').submit();
            } else {
                input.trigger($.Event('keypress', {which: 13}));
            }
        }
    });
});
/*
 * jQuery UI dialogOptions v1.0
 * @desc extending jQuery Ui Dialog - Responsive, click outside, class handling
 * @author Jason Day
 *
 * Dependencies:
 *		jQuery: http://jquery.com/
 *		jQuery UI: http://jqueryui.com/
 *		Modernizr: http://modernizr.com/
 *
 * MIT license:
 *              http://www.opensource.org/licenses/mit-license.php
 *
 * (c) Jason Day 2014
 *
 * New Options:
 *  clickOut: true          // closes dialog when clicked outside
 *  responsive: true        // fluid width & height based on viewport
 *                          // true: always responsive
 *                          // false: never responsive
 *                          // "touch": only responsive on touch device
 *  scaleH: 0.8             // responsive scale height percentage, 0.8 = 80% of viewport
 *  scaleW: 0.8             // responsive scale width percentage, 0.8 = 80% of viewport
 *  showTitleBar: true      // false: hide titlebar
 *  showCloseButton: true   // false: hide close button
 *
 * Added functionality:
 *  add & remove dialogClass to .ui-widget-overlay for scoping styles
 *	patch for: http://bugs.jqueryui.com/ticket/4671
 *	recenter dialog - ajax loaded content
 *
 */

// add new options with default values
$.ui.dialog.prototype.options.clickOut = false;
$.ui.dialog.prototype.options.responsive = true;
$.ui.dialog.prototype.options.scaleH = 0.95;
$.ui.dialog.prototype.options.scaleW = 0.95;
$.ui.dialog.prototype.options.showTitleBar = true;
$.ui.dialog.prototype.options.showCloseButton = true;
$.ui.dialog.prototype.options.beforeDestroy = function() {};
$.ui.dialog.prototype.options.afterDestroy = function() {};


// extend _init
var _init = $.ui.dialog.prototype._init;
$.ui.dialog.prototype._init = function () {
    var self = this;

    // apply original arguments
    _init.apply(this, arguments);

    //patch
    if ($.ui && $.ui.dialog && $.ui.dialog.overlay) {
        $.ui.dialog.overlay.events = $.map('focus,keydown,keypress'.split(','), function (event) {
           return event + '.dialog-overlay';
       }).join(' ');
    }
};
// end _init


// extend open function
var _open = $.ui.dialog.prototype.open;
$.ui.dialog.prototype.open = function () {
    var self = this;


    if ($.rs) {
        $.rs.lockBody();
    }

    //Если ширина задана в процентах
    self.optionWidth = self.element.dialog('option', 'width') + "";
    self.oParentWidth = self.element.parent().outerWidth();
    self.isPercentWidth = self.optionWidth.indexOf('%') > -1;
    // get dialog original size on open
    let optionHeight = self.element.dialog('option', 'height');
    if (typeof optionHeight != 'string' || optionHeight.indexOf('%') == -1) {
        optionHeight = parseInt(optionHeight);
    } else {
        optionHeight = Math.round(document.documentElement.clientHeight * parseInt(optionHeight) / 100);
    }
    self.oHeight = Math.max(optionHeight, self.element.parent().outerHeight());
    self.isTouch = $("html").hasClass("touch");

    // responsive width & height
    var resize = function () {
        // check if responsive
        // dependent on modernizr for device detection / html.touch
        if (self.options.responsive === true || (self.options.responsive === "touch" && self.isTouch)) {

            //Перерасчитываем максимально возможную ширину экрана
            if (self.isPercentWidth ) {
                var calculatedWidth = parseInt(self.optionWidth)/100 * $(window).width();
            } else {
                var calculatedWidth = self.optionWidth;
            }
            self.oWidth = Math.max(parseInt( calculatedWidth ), self.oParentWidth);

            var elem = self.element,
                wHeight = $(window).height(),
                wWidth = $(window).width(),
                dHeight = elem.parent().outerHeight(),
                dWidth = elem.parent().outerWidth(),
                setHeight = Math.min(wHeight * self.options.scaleH, self.oHeight),
                setWidth = Math.min(wWidth * self.options.scaleW, self.oWidth);

            // check & set height
            if ((self.oHeight + 100) > wHeight || elem.hasClass("resizedH")) {
                elem.dialog("option", "height", setHeight).parent().css("max-height", setHeight);
                elem.addClass("resizedH");
            }

            // check & set width
            if ((self.oWidth + 100) > wWidth || elem.hasClass("resizedW")) {
                elem.dialog("option", "width", setWidth).parent().css("max-width", setWidth);
                elem.addClass("resizedW");
            }

            // only recenter & add overflow if dialog has been resized
            if (elem.hasClass("resizedH") || elem.hasClass("resizedW")) {
                elem.dialog("option", "position", {my: "center", at: "center", of: window});
                elem.css("overflow", "auto");
            }
        }

        // add webkit scrolling to all dialogs for touch devices
        if (self.isTouch) {
            elem.css("-webkit-overflow-scrolling", "touch");
        }
    };

    // call resize()
    resize();

    // resize on window resize
    $(window).on("resize", resize);

    self.element.on('dialogclose', function() {
        $(window).off("resize", resize);
    });

    // resize on orientation change
     if (window.addEventListener) {  // Add extra condition because IE8 doesn't support addEventListener (or orientationchange)
        window.addEventListener("orientationchange", function () {
            resize();
        });
    }

    // hide titlebar
    if (!self.options.showTitleBar) {
        self.uiDialogTitlebar.css({
            "height": 0,
            "padding": 0,
            "background": "none",
            "border": 0
        });
        self.uiDialogTitlebar.find(".ui-dialog-title").css("display", "none");
    }

    //hide close button
    if (!self.options.showCloseButton) {
        self.uiDialogTitlebar.find(".ui-dialog-titlebar-close").css("display", "none");
    }

    // close on clickOut
    if (self.options.clickOut && !self.options.modal) {
        // use transparent div - simplest approach (rework)
        $('<div id="dialog-overlay"></div>').insertBefore(self.element.parent());
        $('#dialog-overlay').css({
            "position": "fixed",
            "top": 0,
            "right": 0,
            "bottom": 0,
            "left": 0,
            "background-color": "transparent"
        });
        $('#dialog-overlay').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            self.close();
        });
        // else close on modal click
    } else if (self.options.clickOut && self.options.modal) {
        $('.ui-widget-overlay').click(function (e) {
            self.close();
        });
    }

    // add dialogClass to overlay
    if (self.options.dialogClass) {
        $('.ui-widget-overlay').addClass(self.options.dialogClass);
    }

    // apply original arguments
    _open.apply(this, arguments);
};
//end open


// extend close function
var _close = $.ui.dialog.prototype.close;
$.ui.dialog.prototype.close = function () {
    var self = this;

    // apply original arguments
    _close.apply(this, arguments);

    if ($.rs && $('.ui-widget-overlay').length == 0) {
        $.rs.unlockBody();
    }

    // remove dialogClass to overlay
    if (self.options.dialogClass) {
        $('.ui-widget-overlay').removeClass(self.options.dialogClass);
    }
    //remove clickOut overlay
    if ($("#dialog-overlay").length) {
        $("#dialog-overlay").remove();
    }
};
//end close

var _destroy = $.ui.dialog.prototype._destroy;
$.ui.dialog.prototype._destroy = function () {
    var element = this.element;
    this.options.beforeDestroy.apply(element);
    _destroy.apply(this, arguments);
    this.options.afterDestroy.apply(element);
};

var _setOption = $.ui.dialog.prototype._setOption;
$.ui.dialog.prototype._setOption = function (key, value) {
    if (key == 'originalWidth') {
        if (value.toString().indexOf('%') == -1) {
            this.oWidth = parseInt(value);
        } else {
            this.oWidth = Math.round(document.documentElement.clientWidth * parseInt(value) / 100);
        }
    }
    if (key == 'originalHeight') {
        if (value.toString().indexOf('%') == -1) {
            this.oHeight = parseInt(value);
        } else {
            this.oHeight = Math.round(document.documentElement.clientHeight * parseInt(value) / 100);
        }
    }
    _setOption.apply(this, arguments);
};

//Fix для TinyMCE
var _allowInteraction = $.ui.dialog.prototype._allowInteraction;
$.ui.dialog.prototype._allowInteraction = function( event ) {
    if ($(event.target).closest(".mce-window").length) {
        event.stopPropagation();
        return true;
    } else {
        return _allowInteraction.apply(this, arguments);
    }
};

//Разрешаем использовать html в title диалоговых окон
$.ui.dialog.prototype._title = function(title) {
    if (!this.options.title ) {
        title.html("&#160;");
    } else {
        title.html(this.options.title);
    }
};
/*!
 * Bootstrap v3.3.5 (http://getbootstrap.com)
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under the MIT license
 */
if("undefined"==typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");+function(a){"use strict";var b=a.fn.jquery.split(" ")[0].split(".");if(b[0]<2&&b[1]<9||1==b[0]&&9==b[1]&&b[2]<1)throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher")}(jQuery),+function(a){"use strict";function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one("bsTransitionEnd",function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b(),a.support.transition&&(a.event.special.bsTransitionEnd={bindType:a.support.transition.end,delegateType:a.support.transition.end,handle:function(b){return a(b.target).is(this)?b.handleObj.handler.apply(this,arguments):void 0}})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var c=a(this),e=c.data("bs.alert");e||c.data("bs.alert",e=new d(this)),"string"==typeof b&&e[b].call(c)})}var c='[data-dismiss="alert"]',d=function(b){a(b).on("click",c,this.close)};d.VERSION="3.3.5",d.TRANSITION_DURATION=150,d.prototype.close=function(b){function c(){g.detach().trigger("closed.bs.alert").remove()}var e=a(this),f=e.attr("data-target");f||(f=e.attr("href"),f=f&&f.replace(/.*(?=#[^\s]*$)/,""));var g=a(f);b&&b.preventDefault(),g.length||(g=e.closest(".alert")),g.trigger(b=a.Event("close.bs.alert")),b.isDefaultPrevented()||(g.removeClass("in"),a.support.transition&&g.hasClass("fade")?g.one("bsTransitionEnd",c).emulateTransitionEnd(d.TRANSITION_DURATION):c())};var e=a.fn.alert;a.fn.alert=b,a.fn.alert.Constructor=d,a.fn.alert.noConflict=function(){return a.fn.alert=e,this},a(document).on("click.bs.alert.data-api",c,d.prototype.close)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.button"),f="object"==typeof b&&b;e||d.data("bs.button",e=new c(this,f)),"toggle"==b?e.toggle():b&&e.setState(b)})}var c=function(b,d){this.$element=a(b),this.options=a.extend({},c.DEFAULTS,d),this.isLoading=!1};c.VERSION="3.3.5",c.DEFAULTS={loadingText:"loading..."},c.prototype.setState=function(b){var c="disabled",d=this.$element,e=d.is("input")?"val":"html",f=d.data();b+="Text",null==f.resetText&&d.data("resetText",d[e]()),setTimeout(a.proxy(function(){d[e](null==f[b]?this.options[b]:f[b]),"loadingText"==b?(this.isLoading=!0,d.addClass(c).attr(c,c)):this.isLoading&&(this.isLoading=!1,d.removeClass(c).removeAttr(c))},this),0)},c.prototype.toggle=function(){var a=!0,b=this.$element.closest('[data-toggle="buttons"]');if(b.length){var c=this.$element.find("input");"radio"==c.prop("type")?(c.prop("checked")&&(a=!1),b.find(".active").removeClass("active"),this.$element.addClass("active")):"checkbox"==c.prop("type")&&(c.prop("checked")!==this.$element.hasClass("active")&&(a=!1),this.$element.toggleClass("active")),c.prop("checked",this.$element.hasClass("active")),a&&c.trigger("change")}else this.$element.attr("aria-pressed",!this.$element.hasClass("active")),this.$element.toggleClass("active")};var d=a.fn.button;a.fn.button=b,a.fn.button.Constructor=c,a.fn.button.noConflict=function(){return a.fn.button=d,this},a(document).on("click.bs.button.data-api",'[data-toggle^="button"]',function(c){var d=a(c.target);d.hasClass("btn")||(d=d.closest(".btn")),b.call(d,"toggle"),a(c.target).is('input[type="radio"]')||a(c.target).is('input[type="checkbox"]')||c.preventDefault()}).on("focus.bs.button.data-api blur.bs.button.data-api",'[data-toggle^="button"]',function(b){a(b.target).closest(".btn").toggleClass("focus",/^focus(in)?$/.test(b.type))})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.carousel"),f=a.extend({},c.DEFAULTS,d.data(),"object"==typeof b&&b),g="string"==typeof b?b:f.slide;e||d.data("bs.carousel",e=new c(this,f)),"number"==typeof b?e.to(b):g?e[g]():f.interval&&e.pause().cycle()})}var c=function(b,c){this.$element=a(b),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,this.paused=null,this.sliding=null,this.interval=null,this.$active=null,this.$items=null,this.options.keyboard&&this.$element.on("keydown.bs.carousel",a.proxy(this.keydown,this)),"hover"==this.options.pause&&!("ontouchstart"in document.documentElement)&&this.$element.on("mouseenter.bs.carousel",a.proxy(this.pause,this)).on("mouseleave.bs.carousel",a.proxy(this.cycle,this))};c.VERSION="3.3.5",c.TRANSITION_DURATION=600,c.DEFAULTS={interval:5e3,pause:"hover",wrap:!0,keyboard:!0},c.prototype.keydown=function(a){if(!/input|textarea/i.test(a.target.tagName)){switch(a.which){case 37:this.prev();break;case 39:this.next();break;default:return}a.preventDefault()}},c.prototype.cycle=function(b){return b||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},c.prototype.getItemIndex=function(a){return this.$items=a.parent().children(".item"),this.$items.index(a||this.$active)},c.prototype.getItemForDirection=function(a,b){var c=this.getItemIndex(b),d="prev"==a&&0===c||"next"==a&&c==this.$items.length-1;if(d&&!this.options.wrap)return b;var e="prev"==a?-1:1,f=(c+e)%this.$items.length;return this.$items.eq(f)},c.prototype.to=function(a){var b=this,c=this.getItemIndex(this.$active=this.$element.find(".item.active"));return a>this.$items.length-1||0>a?void 0:this.sliding?this.$element.one("slid.bs.carousel",function(){b.to(a)}):c==a?this.pause().cycle():this.slide(a>c?"next":"prev",this.$items.eq(a))},c.prototype.pause=function(b){return b||(this.paused=!0),this.$element.find(".next, .prev").length&&a.support.transition&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),this.interval=clearInterval(this.interval),this},c.prototype.next=function(){return this.sliding?void 0:this.slide("next")},c.prototype.prev=function(){return this.sliding?void 0:this.slide("prev")},c.prototype.slide=function(b,d){var e=this.$element.find(".item.active"),f=d||this.getItemForDirection(b,e),g=this.interval,h="next"==b?"left":"right",i=this;if(f.hasClass("active"))return this.sliding=!1;var j=f[0],k=a.Event("slide.bs.carousel",{relatedTarget:j,direction:h});if(this.$element.trigger(k),!k.isDefaultPrevented()){if(this.sliding=!0,g&&this.pause(),this.$indicators.length){this.$indicators.find(".active").removeClass("active");var l=a(this.$indicators.children()[this.getItemIndex(f)]);l&&l.addClass("active")}var m=a.Event("slid.bs.carousel",{relatedTarget:j,direction:h});return a.support.transition&&this.$element.hasClass("slide")?(f.addClass(b),f[0].offsetWidth,e.addClass(h),f.addClass(h),e.one("bsTransitionEnd",function(){f.removeClass([b,h].join(" ")).addClass("active"),e.removeClass(["active",h].join(" ")),i.sliding=!1,setTimeout(function(){i.$element.trigger(m)},0)}).emulateTransitionEnd(c.TRANSITION_DURATION)):(e.removeClass("active"),f.addClass("active"),this.sliding=!1,this.$element.trigger(m)),g&&this.cycle(),this}};var d=a.fn.carousel;a.fn.carousel=b,a.fn.carousel.Constructor=c,a.fn.carousel.noConflict=function(){return a.fn.carousel=d,this};var e=function(c){var d,e=a(this),f=a(e.attr("data-target")||(d=e.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""));if(f.hasClass("carousel")){var g=a.extend({},f.data(),e.data()),h=e.attr("data-slide-to");h&&(g.interval=!1),b.call(f,g),h&&f.data("bs.carousel").to(h),c.preventDefault()}};a(document).on("click.bs.carousel.data-api","[data-slide]",e).on("click.bs.carousel.data-api","[data-slide-to]",e),a(window).on("load",function(){a('[data-ride="carousel"]').each(function(){var c=a(this);b.call(c,c.data())})})}(jQuery),+function(a){"use strict";function b(b){var c,d=b.attr("data-target")||(c=b.attr("href"))&&c.replace(/.*(?=#[^\s]+$)/,"");return a(d)}function c(b){return this.each(function(){var c=a(this),e=c.data("bs.collapse"),f=a.extend({},d.DEFAULTS,c.data(),"object"==typeof b&&b);!e&&f.toggle&&/show|hide/.test(b)&&(f.toggle=!1),e||c.data("bs.collapse",e=new d(this,f)),"string"==typeof b&&e[b]()})}var d=function(b,c){this.$element=a(b),this.options=a.extend({},d.DEFAULTS,c),this.$trigger=a('[data-toggle="collapse"][href="#'+b.id+'"],[data-toggle="collapse"][data-target="#'+b.id+'"]'),this.transitioning=null,this.options.parent?this.$parent=this.getParent():this.addAriaAndCollapsedClass(this.$element,this.$trigger),this.options.toggle&&this.toggle()};d.VERSION="3.3.5",d.TRANSITION_DURATION=350,d.DEFAULTS={toggle:!0},d.prototype.dimension=function(){var a=this.$element.hasClass("width");return a?"width":"height"},d.prototype.show=function(){if(!this.transitioning&&!this.$element.hasClass("in")){var b,e=this.$parent&&this.$parent.children(".panel").children(".in, .collapsing");if(!(e&&e.length&&(b=e.data("bs.collapse"),b&&b.transitioning))){var f=a.Event("show.bs.collapse");if(this.$element.trigger(f),!f.isDefaultPrevented()){e&&e.length&&(c.call(e,"hide"),b||e.data("bs.collapse",null));var g=this.dimension();this.$element.removeClass("collapse").addClass("collapsing")[g](0).attr("aria-expanded",!0),this.$trigger.removeClass("collapsed").attr("aria-expanded",!0),this.transitioning=1;var h=function(){this.$element.removeClass("collapsing").addClass("collapse in")[g](""),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!a.support.transition)return h.call(this);var i=a.camelCase(["scroll",g].join("-"));this.$element.one("bsTransitionEnd",a.proxy(h,this)).emulateTransitionEnd(d.TRANSITION_DURATION)[g](this.$element[0][i])}}}},d.prototype.hide=function(){if(!this.transitioning&&this.$element.hasClass("in")){var b=a.Event("hide.bs.collapse");if(this.$element.trigger(b),!b.isDefaultPrevented()){var c=this.dimension();this.$element[c](this.$element[c]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded",!1),this.$trigger.addClass("collapsed").attr("aria-expanded",!1),this.transitioning=1;var e=function(){this.transitioning=0,this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")};return a.support.transition?void this.$element[c](0).one("bsTransitionEnd",a.proxy(e,this)).emulateTransitionEnd(d.TRANSITION_DURATION):e.call(this)}}},d.prototype.toggle=function(){this[this.$element.hasClass("in")?"hide":"show"]()},d.prototype.getParent=function(){return a(this.options.parent).find('[data-toggle="collapse"][data-parent="'+this.options.parent+'"]').each(a.proxy(function(c,d){var e=a(d);this.addAriaAndCollapsedClass(b(e),e)},this)).end()},d.prototype.addAriaAndCollapsedClass=function(a,b){var c=a.hasClass("in");a.attr("aria-expanded",c),b.toggleClass("collapsed",!c).attr("aria-expanded",c)};var e=a.fn.collapse;a.fn.collapse=c,a.fn.collapse.Constructor=d,a.fn.collapse.noConflict=function(){return a.fn.collapse=e,this},a(document).on("click.bs.collapse.data-api",'[data-toggle="collapse"]',function(d){var e=a(this);e.attr("data-target")||d.preventDefault();var f=b(e),g=f.data("bs.collapse"),h=g?"toggle":e.data();c.call(f,h)})}(jQuery),+function(a){"use strict";function b(b){var c=b.attr("data-target");c||(c=b.attr("href"),c=c&&/#[A-Za-z]/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,""));var d=c&&a(c);return d&&d.length?d:b.parent()}function c(c){c&&3===c.which||(a(e).remove(),a(f).each(function(){var d=a(this),e=b(d),f={relatedTarget:this};e.hasClass("open")&&(c&&"click"==c.type&&/input|textarea/i.test(c.target.tagName)&&a.contains(e[0],c.target)||(e.trigger(c=a.Event("hide.bs.dropdown",f)),c.isDefaultPrevented()||(d.attr("aria-expanded","false"),e.removeClass("open").trigger("hidden.bs.dropdown",f))))}))}function d(b){return this.each(function(){var c=a(this),d=c.data("bs.dropdown");d||c.data("bs.dropdown",d=new g(this)),"string"==typeof b&&d[b].call(c)})}var e=".dropdown-backdrop",f='[data-toggle="dropdown"]',g=function(b){a(b).on("click.bs.dropdown",this.toggle)};g.VERSION="3.3.5",g.prototype.toggle=function(d){var e=a(this);if(!e.is(".disabled, :disabled")){var f=b(e),g=f.hasClass("open");if(c(),!g){"ontouchstart"in document.documentElement&&!f.closest(".navbar-nav").length&&a(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(a(this)).on("click",c);var h={relatedTarget:this};if(f.trigger(d=a.Event("show.bs.dropdown",h)),d.isDefaultPrevented())return;e.trigger("focus").attr("aria-expanded","true"),f.toggleClass("open").trigger("shown.bs.dropdown",h)}return!1}},g.prototype.keydown=function(c){if(/(38|40|27|32)/.test(c.which)&&!/input|textarea/i.test(c.target.tagName)){var d=a(this);if(c.preventDefault(),c.stopPropagation(),!d.is(".disabled, :disabled")){var e=b(d),g=e.hasClass("open");if(!g&&27!=c.which||g&&27==c.which)return 27==c.which&&e.find(f).trigger("focus"),d.trigger("click");var h=" li:not(.disabled):visible a",i=e.find(".dropdown-menu"+h);if(i.length){var j=i.index(c.target);38==c.which&&j>0&&j--,40==c.which&&j<i.length-1&&j++,~j||(j=0),i.eq(j).trigger("focus")}}}};var h=a.fn.dropdown;a.fn.dropdown=d,a.fn.dropdown.Constructor=g,a.fn.dropdown.noConflict=function(){return a.fn.dropdown=h,this},a(document).on("click.bs.dropdown.data-api",c).on("click.bs.dropdown.data-api",".dropdown form",function(a){a.stopPropagation()}).on("click.bs.dropdown.data-api",f,g.prototype.toggle).on("keydown.bs.dropdown.data-api",f,g.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",g.prototype.keydown)}(jQuery),+function(a){"use strict";function b(b,d){return this.each(function(){var e=a(this),f=e.data("bs.modal"),g=a.extend({},c.DEFAULTS,e.data(),"object"==typeof b&&b);f||e.data("bs.modal",f=new c(this,g)),"string"==typeof b?f[b](d):g.show&&f.show(d)})}var c=function(b,c){this.options=c,this.$body=a(document.body),this.$element=a(b),this.$dialog=this.$element.find(".modal-dialog"),this.$backdrop=null,this.isShown=null,this.originalBodyPad=null,this.scrollbarWidth=0,this.ignoreBackdropClick=!1,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};c.VERSION="3.3.5",c.TRANSITION_DURATION=300,c.BACKDROP_TRANSITION_DURATION=150,c.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},c.prototype.toggle=function(a){return this.isShown?this.hide():this.show(a)},c.prototype.show=function(b){var d=this,e=a.Event("show.bs.modal",{relatedTarget:b});this.$element.trigger(e),this.isShown||e.isDefaultPrevented()||(this.isShown=!0,this.checkScrollbar(),this.setScrollbar(),this.$body.addClass("modal-open"),this.escape(),this.resize(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.$dialog.on("mousedown.dismiss.bs.modal",function(){d.$element.one("mouseup.dismiss.bs.modal",function(b){a(b.target).is(d.$element)&&(d.ignoreBackdropClick=!0)})}),this.backdrop(function(){var e=a.support.transition&&d.$element.hasClass("fade");d.$element.parent().length||d.$element.appendTo(d.$body),d.$element.show().scrollTop(0),d.adjustDialog(),e&&d.$element[0].offsetWidth,d.$element.addClass("in"),d.enforceFocus();var f=a.Event("shown.bs.modal",{relatedTarget:b});e?d.$dialog.one("bsTransitionEnd",function(){d.$element.trigger("focus").trigger(f)}).emulateTransitionEnd(c.TRANSITION_DURATION):d.$element.trigger("focus").trigger(f)}))},c.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.escape(),this.resize(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"),this.$dialog.off("mousedown.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",a.proxy(this.hideModal,this)).emulateTransitionEnd(c.TRANSITION_DURATION):this.hideModal())},c.prototype.enforceFocus=function(){a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){this.$element[0]===a.target||this.$element.has(a.target).length||this.$element.trigger("focus")},this))},c.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keydown.dismiss.bs.modal",a.proxy(function(a){27==a.which&&this.hide()},this)):this.isShown||this.$element.off("keydown.dismiss.bs.modal")},c.prototype.resize=function(){this.isShown?a(window).on("resize.bs.modal",a.proxy(this.handleUpdate,this)):a(window).off("resize.bs.modal")},c.prototype.hideModal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.$body.removeClass("modal-open"),a.resetAdjustments(),a.resetScrollbar(),a.$element.trigger("hidden.bs.modal")})},c.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},c.prototype.backdrop=function(b){var d=this,e=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var f=a.support.transition&&e;if(this.$backdrop=a(document.createElement("div")).addClass("modal-backdrop "+e).appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){return this.ignoreBackdropClick?void(this.ignoreBackdropClick=!1):void(a.target===a.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus():this.hide()))},this)),f&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;f?this.$backdrop.one("bsTransitionEnd",b).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):b()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass("in");var g=function(){d.removeBackdrop(),b&&b()};a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",g).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):g()}else b&&b()},c.prototype.handleUpdate=function(){this.adjustDialog()},c.prototype.adjustDialog=function(){var a=this.$element[0].scrollHeight>document.documentElement.clientHeight;this.$element.css({paddingLeft:!this.bodyIsOverflowing&&a?this.scrollbarWidth:"",paddingRight:this.bodyIsOverflowing&&!a?this.scrollbarWidth:""})},c.prototype.resetAdjustments=function(){this.$element.css({paddingLeft:"",paddingRight:""})},c.prototype.checkScrollbar=function(){var a=window.innerWidth;if(!a){var b=document.documentElement.getBoundingClientRect();a=b.right-Math.abs(b.left)}this.bodyIsOverflowing=document.body.clientWidth<a,this.scrollbarWidth=this.measureScrollbar()},c.prototype.setScrollbar=function(){var a=parseInt(this.$body.css("padding-right")||0,10);this.originalBodyPad=document.body.style.paddingRight||"",this.bodyIsOverflowing&&this.$body.css("padding-right",a+this.scrollbarWidth)},c.prototype.resetScrollbar=function(){this.$body.css("padding-right",this.originalBodyPad)},c.prototype.measureScrollbar=function(){var a=document.createElement("div");a.className="modal-scrollbar-measure",this.$body.append(a);var b=a.offsetWidth-a.clientWidth;return this.$body[0].removeChild(a),b};var d=a.fn.modal;a.fn.modal=b,a.fn.modal.Constructor=c,a.fn.modal.noConflict=function(){return a.fn.modal=d,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(c){var d=a(this),e=d.attr("href"),f=a(d.attr("data-target")||e&&e.replace(/.*(?=#[^\s]+$)/,"")),g=f.data("bs.modal")?"toggle":a.extend({remote:!/#/.test(e)&&e},f.data(),d.data());d.is("a")&&c.preventDefault(),f.one("show.bs.modal",function(a){a.isDefaultPrevented()||f.one("hidden.bs.modal",function(){d.is(":visible")&&d.trigger("focus")})}),b.call(f,g,this)})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tooltip"),f="object"==typeof b&&b;(e||!/destroy|hide/.test(b))&&(e||d.data("bs.tooltip",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.type=null,this.options=null,this.enabled=null,this.timeout=null,this.hoverState=null,this.$element=null,this.inState=null,this.init("tooltip",a,b)};c.VERSION="3.3.5",c.TRANSITION_DURATION=150,c.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0}},c.prototype.init=function(b,c,d){if(this.enabled=!0,this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.$viewport=this.options.viewport&&a(a.isFunction(this.options.viewport)?this.options.viewport.call(this,this.$element):this.options.viewport.selector||this.options.viewport),this.inState={click:!1,hover:!1,focus:!1},this.$element[0]instanceof document.constructor&&!this.options.selector)throw new Error("`selector` option must be specified when initializing "+this.type+" on the window.document object!");for(var e=this.options.trigger.split(" "),f=e.length;f--;){var g=e[f];if("click"==g)this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this));else if("manual"!=g){var h="hover"==g?"mouseenter":"focusin",i="hover"==g?"mouseleave":"focusout";this.$element.on(h+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(i+"."+this.type,this.options.selector,a.proxy(this.leave,this))}}this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.getOptions=function(b){return b=a.extend({},this.getDefaults(),this.$element.data(),b),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},c.prototype.getDelegateOptions=function(){var b={},c=this.getDefaults();return this._options&&a.each(this._options,function(a,d){c[a]!=d&&(b[a]=d)}),b},c.prototype.enter=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusin"==b.type?"focus":"hover"]=!0),c.tip().hasClass("in")||"in"==c.hoverState?void(c.hoverState="in"):(clearTimeout(c.timeout),c.hoverState="in",c.options.delay&&c.options.delay.show?void(c.timeout=setTimeout(function(){"in"==c.hoverState&&c.show()},c.options.delay.show)):c.show())},c.prototype.isInStateTrue=function(){for(var a in this.inState)if(this.inState[a])return!0;return!1},c.prototype.leave=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusout"==b.type?"focus":"hover"]=!1),c.isInStateTrue()?void 0:(clearTimeout(c.timeout),c.hoverState="out",c.options.delay&&c.options.delay.hide?void(c.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide)):c.hide())},c.prototype.show=function(){var b=a.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(b);var d=a.contains(this.$element[0].ownerDocument.documentElement,this.$element[0]);if(b.isDefaultPrevented()||!d)return;var e=this,f=this.tip(),g=this.getUID(this.type);this.setContent(),f.attr("id",g),this.$element.attr("aria-describedby",g),this.options.animation&&f.addClass("fade");var h="function"==typeof this.options.placement?this.options.placement.call(this,f[0],this.$element[0]):this.options.placement,i=/\s?auto?\s?/i,j=i.test(h);j&&(h=h.replace(i,"")||"top"),f.detach().css({top:0,left:0,display:"block"}).addClass(h).data("bs."+this.type,this),this.options.container?f.appendTo(this.options.container):f.insertAfter(this.$element),this.$element.trigger("inserted.bs."+this.type);var k=this.getPosition(),l=f[0].offsetWidth,m=f[0].offsetHeight;if(j){var n=h,o=this.getPosition(this.$viewport);h="bottom"==h&&k.bottom+m>o.bottom?"top":"top"==h&&k.top-m<o.top?"bottom":"right"==h&&k.right+l>o.width?"left":"left"==h&&k.left-l<o.left?"right":h,f.removeClass(n).addClass(h)}var p=this.getCalculatedOffset(h,k,l,m);this.applyPlacement(p,h);var q=function(){var a=e.hoverState;e.$element.trigger("shown.bs."+e.type),e.hoverState=null,"out"==a&&e.leave(e)};a.support.transition&&this.$tip.hasClass("fade")?f.one("bsTransitionEnd",q).emulateTransitionEnd(c.TRANSITION_DURATION):q()}},c.prototype.applyPlacement=function(b,c){var d=this.tip(),e=d[0].offsetWidth,f=d[0].offsetHeight,g=parseInt(d.css("margin-top"),10),h=parseInt(d.css("margin-left"),10);isNaN(g)&&(g=0),isNaN(h)&&(h=0),b.top+=g,b.left+=h,a.offset.setOffset(d[0],a.extend({using:function(a){d.css({top:Math.round(a.top),left:Math.round(a.left)})}},b),0),d.addClass("in");var i=d[0].offsetWidth,j=d[0].offsetHeight;"top"==c&&j!=f&&(b.top=b.top+f-j);var k=this.getViewportAdjustedDelta(c,b,i,j);k.left?b.left+=k.left:b.top+=k.top;var l=/top|bottom/.test(c),m=l?2*k.left-e+i:2*k.top-f+j,n=l?"offsetWidth":"offsetHeight";d.offset(b),this.replaceArrow(m,d[0][n],l)},c.prototype.replaceArrow=function(a,b,c){this.arrow().css(c?"left":"top",50*(1-a/b)+"%").css(c?"top":"left","")},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},c.prototype.hide=function(b){function d(){"in"!=e.hoverState&&f.detach(),e.$element.removeAttr("aria-describedby").trigger("hidden.bs."+e.type),b&&b()}var e=this,f=a(this.$tip),g=a.Event("hide.bs."+this.type);return this.$element.trigger(g),g.isDefaultPrevented()?void 0:(f.removeClass("in"),a.support.transition&&f.hasClass("fade")?f.one("bsTransitionEnd",d).emulateTransitionEnd(c.TRANSITION_DURATION):d(),this.hoverState=null,this)},c.prototype.fixTitle=function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},c.prototype.hasContent=function(){return this.getTitle()},c.prototype.getPosition=function(b){b=b||this.$element;var c=b[0],d="BODY"==c.tagName,e=c.getBoundingClientRect();null==e.width&&(e=a.extend({},e,{width:e.right-e.left,height:e.bottom-e.top}));var f=d?{top:0,left:0}:b.offset(),g={scroll:d?document.documentElement.scrollTop||document.body.scrollTop:b.scrollTop()},h=d?{width:a(window).width(),height:a(window).height()}:null;return a.extend({},e,g,h,f)},c.prototype.getCalculatedOffset=function(a,b,c,d){return"bottom"==a?{top:b.top+b.height,left:b.left+b.width/2-c/2}:"top"==a?{top:b.top-d,left:b.left+b.width/2-c/2}:"left"==a?{top:b.top+b.height/2-d/2,left:b.left-c}:{top:b.top+b.height/2-d/2,left:b.left+b.width}},c.prototype.getViewportAdjustedDelta=function(a,b,c,d){var e={top:0,left:0};if(!this.$viewport)return e;var f=this.options.viewport&&this.options.viewport.padding||0,g=this.getPosition(this.$viewport);if(/right|left/.test(a)){var h=b.top-f-g.scroll,i=b.top+f-g.scroll+d;h<g.top?e.top=g.top-h:i>g.top+g.height&&(e.top=g.top+g.height-i)}else{var j=b.left-f,k=b.left+f+c;j<g.left?e.left=g.left-j:k>g.right&&(e.left=g.left+g.width-k)}return e},c.prototype.getTitle=function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},c.prototype.getUID=function(a){do a+=~~(1e6*Math.random());while(document.getElementById(a));return a},c.prototype.tip=function(){if(!this.$tip&&(this.$tip=a(this.options.template),1!=this.$tip.length))throw new Error(this.type+" `template` option must consist of exactly 1 top-level element!");return this.$tip},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},c.prototype.enable=function(){this.enabled=!0},c.prototype.disable=function(){this.enabled=!1},c.prototype.toggleEnabled=function(){this.enabled=!this.enabled},c.prototype.toggle=function(b){var c=this;b&&(c=a(b.currentTarget).data("bs."+this.type),c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c))),b?(c.inState.click=!c.inState.click,c.isInStateTrue()?c.enter(c):c.leave(c)):c.tip().hasClass("in")?c.leave(c):c.enter(c)},c.prototype.destroy=function(){var a=this;clearTimeout(this.timeout),this.hide(function(){a.$element.off("."+a.type).removeData("bs."+a.type),a.$tip&&a.$tip.detach(),a.$tip=null,a.$arrow=null,a.$viewport=null})};var d=a.fn.tooltip;a.fn.tooltip=b,a.fn.tooltip.Constructor=c,a.fn.tooltip.noConflict=function(){return a.fn.tooltip=d,this}}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.popover"),f="object"==typeof b&&b;(e||!/destroy|hide/.test(b))&&(e||d.data("bs.popover",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.init("popover",a,b)};if(!a.fn.tooltip)throw new Error("Popover requires tooltip.js");c.VERSION="3.3.5",c.DEFAULTS=a.extend({},a.fn.tooltip.Constructor.DEFAULTS,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),c.prototype=a.extend({},a.fn.tooltip.Constructor.prototype),c.prototype.constructor=c,c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content").children().detach().end()[this.options.html?"string"==typeof c?"html":"append":"text"](c),a.removeClass("fade top bottom left right in"),a.find(".popover-title").html()||a.find(".popover-title").hide()},c.prototype.hasContent=function(){return this.getTitle()||this.getContent()},c.prototype.getContent=function(){var a=this.$element,b=this.options;return a.attr("data-content")||("function"==typeof b.content?b.content.call(a[0]):b.content)},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".arrow")};var d=a.fn.popover;a.fn.popover=b,a.fn.popover.Constructor=c,a.fn.popover.noConflict=function(){return a.fn.popover=d,this}}(jQuery),+function(a){"use strict";function b(c,d){this.$body=a(document.body),this.$scrollElement=a(a(c).is(document.body)?window:c),this.options=a.extend({},b.DEFAULTS,d),this.selector=(this.options.target||"")+" .nav li > a",this.offsets=[],this.targets=[],this.activeTarget=null,this.scrollHeight=0,this.$scrollElement.on("scroll.bs.scrollspy",a.proxy(this.process,this)),this.refresh(),this.process()}function c(c){return this.each(function(){var d=a(this),e=d.data("bs.scrollspy"),f="object"==typeof c&&c;e||d.data("bs.scrollspy",e=new b(this,f)),"string"==typeof c&&e[c]()})}b.VERSION="3.3.5",b.DEFAULTS={offset:10},b.prototype.getScrollHeight=function(){return this.$scrollElement[0].scrollHeight||Math.max(this.$body[0].scrollHeight,document.documentElement.scrollHeight)},b.prototype.refresh=function(){var b=this,c="offset",d=0;this.offsets=[],this.targets=[],this.scrollHeight=this.getScrollHeight(),a.isWindow(this.$scrollElement[0])||(c="position",d=this.$scrollElement.scrollTop()),this.$body.find(this.selector).map(function(){var b=a(this),e=b.data("target")||b.attr("href"),f=/^#./.test(e)&&a(e);return f&&f.length&&f.is(":visible")&&[[f[c]().top+d,e]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){b.offsets.push(this[0]),b.targets.push(this[1])})},b.prototype.process=function(){var a,b=this.$scrollElement.scrollTop()+this.options.offset,c=this.getScrollHeight(),d=this.options.offset+c-this.$scrollElement.height(),e=this.offsets,f=this.targets,g=this.activeTarget;if(this.scrollHeight!=c&&this.refresh(),b>=d)return g!=(a=f[f.length-1])&&this.activate(a);if(g&&b<e[0])return this.activeTarget=null,this.clear();for(a=e.length;a--;)g!=f[a]&&b>=e[a]&&(void 0===e[a+1]||b<e[a+1])&&this.activate(f[a])},b.prototype.activate=function(b){this.activeTarget=b,this.clear();var c=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),
d.trigger("activate.bs.scrollspy")},b.prototype.clear=function(){a(this.selector).parentsUntil(this.options.target,".active").removeClass("active")};var d=a.fn.scrollspy;a.fn.scrollspy=c,a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){return a.fn.scrollspy=d,this},a(window).on("load.bs.scrollspy.data-api",function(){a('[data-spy="scroll"]').each(function(){var b=a(this);c.call(b,b.data())})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tab");e||d.data("bs.tab",e=new c(this)),"string"==typeof b&&e[b]()})}var c=function(b){this.element=a(b)};c.VERSION="3.3.5",c.TRANSITION_DURATION=150,c.prototype.show=function(){var b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.data("target");if(d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),!b.parent("li").hasClass("active")){var e=c.find(".active:last a"),f=a.Event("hide.bs.tab",{relatedTarget:b[0]}),g=a.Event("show.bs.tab",{relatedTarget:e[0]});if(e.trigger(f),b.trigger(g),!g.isDefaultPrevented()&&!f.isDefaultPrevented()){var h=a(d);this.activate(b.closest("li"),c),this.activate(h,h.parent(),function(){e.trigger({type:"hidden.bs.tab",relatedTarget:b[0]}),b.trigger({type:"shown.bs.tab",relatedTarget:e[0]})})}}},c.prototype.activate=function(b,d,e){function f(){g.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),b.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),h?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu").length&&b.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),e&&e()}var g=d.find("> .active"),h=e&&a.support.transition&&(g.length&&g.hasClass("fade")||!!d.find("> .fade").length);g.length&&h?g.one("bsTransitionEnd",f).emulateTransitionEnd(c.TRANSITION_DURATION):f(),g.removeClass("in")};var d=a.fn.tab;a.fn.tab=b,a.fn.tab.Constructor=c,a.fn.tab.noConflict=function(){return a.fn.tab=d,this};var e=function(c){c.preventDefault(),b.call(a(this),"show")};a(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',e).on("click.bs.tab.data-api",'[data-toggle="pill"]',e)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.affix"),f="object"==typeof b&&b;e||d.data("bs.affix",e=new c(this,f)),"string"==typeof b&&e[b]()})}var c=function(b,d){this.options=a.extend({},c.DEFAULTS,d),this.$target=a(this.options.target).on("scroll.bs.affix.data-api",a.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",a.proxy(this.checkPositionWithEventLoop,this)),this.$element=a(b),this.affixed=null,this.unpin=null,this.pinnedOffset=null,this.checkPosition()};c.VERSION="3.3.5",c.RESET="affix affix-top affix-bottom",c.DEFAULTS={offset:0,target:window},c.prototype.getState=function(a,b,c,d){var e=this.$target.scrollTop(),f=this.$element.offset(),g=this.$target.height();if(null!=c&&"top"==this.affixed)return c>e?"top":!1;if("bottom"==this.affixed)return null!=c?e+this.unpin<=f.top?!1:"bottom":a-d>=e+g?!1:"bottom";var h=null==this.affixed,i=h?e:f.top,j=h?g:b;return null!=c&&c>=e?"top":null!=d&&i+j>=a-d?"bottom":!1},c.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(c.RESET).addClass("affix");var a=this.$target.scrollTop(),b=this.$element.offset();return this.pinnedOffset=b.top-a},c.prototype.checkPositionWithEventLoop=function(){setTimeout(a.proxy(this.checkPosition,this),1)},c.prototype.checkPosition=function(){if(this.$element.is(":visible")){var b=this.$element.height(),d=this.options.offset,e=d.top,f=d.bottom,g=Math.max(a(document).height(),a(document.body).height());"object"!=typeof d&&(f=e=d),"function"==typeof e&&(e=d.top(this.$element)),"function"==typeof f&&(f=d.bottom(this.$element));var h=this.getState(g,b,e,f);if(this.affixed!=h){null!=this.unpin&&this.$element.css("top","");var i="affix"+(h?"-"+h:""),j=a.Event(i+".bs.affix");if(this.$element.trigger(j),j.isDefaultPrevented())return;this.affixed=h,this.unpin="bottom"==h?this.getPinnedOffset():null,this.$element.removeClass(c.RESET).addClass(i).trigger(i.replace("affix","affixed")+".bs.affix")}"bottom"==h&&this.$element.offset({top:g-b-f})}};var d=a.fn.affix;a.fn.affix=b,a.fn.affix.Constructor=c,a.fn.affix.noConflict=function(){return a.fn.affix=d,this},a(window).on("load",function(){a('[data-spy="affix"]').each(function(){var c=a(this),d=c.data();d.offset=d.offset||{},null!=d.offsetBottom&&(d.offset.bottom=d.offsetBottom),null!=d.offsetTop&&(d.offset.top=d.offsetTop),b.call(c,d)})})}(jQuery);
/* == jquery mousewheel plugin == Version: 3.1.12, License: MIT License (MIT) */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a:a(jQuery)}(function(a){function b(b){var g=b||window.event,h=i.call(arguments,1),j=0,l=0,m=0,n=0,o=0,p=0;if(b=a.event.fix(g),b.type="mousewheel","detail"in g&&(m=-1*g.detail),"wheelDelta"in g&&(m=g.wheelDelta),"wheelDeltaY"in g&&(m=g.wheelDeltaY),"wheelDeltaX"in g&&(l=-1*g.wheelDeltaX),"axis"in g&&g.axis===g.HORIZONTAL_AXIS&&(l=-1*m,m=0),j=0===m?l:m,"deltaY"in g&&(m=-1*g.deltaY,j=m),"deltaX"in g&&(l=g.deltaX,0===m&&(j=-1*l)),0!==m||0!==l){if(1===g.deltaMode){var q=a.data(this,"mousewheel-line-height");j*=q,m*=q,l*=q}else if(2===g.deltaMode){var r=a.data(this,"mousewheel-page-height");j*=r,m*=r,l*=r}if(n=Math.max(Math.abs(m),Math.abs(l)),(!f||f>n)&&(f=n,d(g,n)&&(f/=40)),d(g,n)&&(j/=40,l/=40,m/=40),j=Math[j>=1?"floor":"ceil"](j/f),l=Math[l>=1?"floor":"ceil"](l/f),m=Math[m>=1?"floor":"ceil"](m/f),k.settings.normalizeOffset&&this.getBoundingClientRect){var s=this.getBoundingClientRect();o=b.clientX-s.left,p=b.clientY-s.top}return b.deltaX=l,b.deltaY=m,b.deltaFactor=f,b.offsetX=o,b.offsetY=p,b.deltaMode=0,h.unshift(b,j,l,m),e&&clearTimeout(e),e=setTimeout(c,200),(a.event.dispatch||a.event.handle).apply(this,h)}}function c(){f=null}function d(a,b){return k.settings.adjustOldDeltas&&"mousewheel"===a.type&&b%120===0}var e,f,g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],h="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(a.event.fixHooks)for(var j=g.length;j;)a.event.fixHooks[g[--j]]=a.event.mouseHooks;var k=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var c=h.length;c;)this.addEventListener(h[--c],b,!1);else this.onmousewheel=b;a.data(this,"mousewheel-line-height",k.getLineHeight(this)),a.data(this,"mousewheel-page-height",k.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var c=h.length;c;)this.removeEventListener(h[--c],b,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(b){var c=a(b),d=c["offsetParent"in a.fn?"offsetParent":"parent"]();return d.length||(d=a("body")),parseInt(d.css("fontSize"),10)||parseInt(c.css("fontSize"),10)||16},getPageHeight:function(b){return a(b).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})});
/* == malihu jquery custom scrollbar plugin == Version: 3.0.9, License: MIT License (MIT) */
!function(e){"undefined"!=typeof module&&module.exports?module.exports=e:e(jQuery,window,document)}(function(e){!function(t){var o="function"==typeof define&&define.amd,a="undefined"!=typeof module&&module.exports,n="https:"==document.location.protocol?"https:":"http:",i="cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.12/jquery.mousewheel.min.js";o||(a?require("jquery-mousewheel")(e):e.event.special.mousewheel||e("head").append(decodeURI("%3Cscript src="+n+"//"+i+"%3E%3C/script%3E"))),t()}(function(){var t,o="mCustomScrollbar",a="mCS",n=".mCustomScrollbar",i={setTop:0,setLeft:0,axis:"y",scrollbarPosition:"inside",scrollInertia:950,autoDraggerLength:!0,alwaysShowScrollbar:0,snapOffset:0,mouseWheel:{enable:!0,scrollAmount:"auto",axis:"y",deltaFactor:"auto",disableOver:["select","option","keygen","datalist","textarea"]},scrollButtons:{scrollType:"stepless",scrollAmount:"auto"},keyboard:{enable:!0,scrollType:"stepless",scrollAmount:"auto"},contentTouchScroll:25,advanced:{autoScrollOnFocus:"input,textarea,select,button,datalist,keygen,a[tabindex],area,object,[contenteditable='true']",updateOnContentResize:!0,updateOnImageLoad:!0,autoUpdateTimeout:60},theme:"light",callbacks:{onTotalScrollOffset:0,onTotalScrollBackOffset:0,alwaysTriggerOffsets:!0}},r=0,l={},s=window.attachEvent&&!window.addEventListener?1:0,c=!1,d=["mCSB_dragger_onDrag","mCSB_scrollTools_onDrag","mCS_img_loaded","mCS_disabled","mCS_destroyed","mCS_no_scrollbar","mCS-autoHide","mCS-dir-rtl","mCS_no_scrollbar_y","mCS_no_scrollbar_x","mCS_y_hidden","mCS_x_hidden","mCSB_draggerContainer","mCSB_buttonUp","mCSB_buttonDown","mCSB_buttonLeft","mCSB_buttonRight"],u={init:function(t){var t=e.extend(!0,{},i,t),o=f.call(this);if(t.live){var s=t.liveSelector||this.selector||n,c=e(s);if("off"===t.live)return void m(s);l[s]=setTimeout(function(){c.mCustomScrollbar(t),"once"===t.live&&c.length&&m(s)},500)}else m(s);return t.setWidth=t.set_width?t.set_width:t.setWidth,t.setHeight=t.set_height?t.set_height:t.setHeight,t.axis=t.horizontalScroll?"x":p(t.axis),t.scrollInertia=t.scrollInertia>0&&t.scrollInertia<17?17:t.scrollInertia,"object"!=typeof t.mouseWheel&&1==t.mouseWheel&&(t.mouseWheel={enable:!0,scrollAmount:"auto",axis:"y",preventDefault:!1,deltaFactor:"auto",normalizeDelta:!1,invert:!1}),t.mouseWheel.scrollAmount=t.mouseWheelPixels?t.mouseWheelPixels:t.mouseWheel.scrollAmount,t.mouseWheel.normalizeDelta=t.advanced.normalizeMouseWheelDelta?t.advanced.normalizeMouseWheelDelta:t.mouseWheel.normalizeDelta,t.scrollButtons.scrollType=g(t.scrollButtons.scrollType),h(t),e(o).each(function(){var o=e(this);if(!o.data(a)){o.data(a,{idx:++r,opt:t,scrollRatio:{y:null,x:null},overflowed:null,contentReset:{y:null,x:null},bindEvents:!1,tweenRunning:!1,sequential:{},langDir:o.css("direction"),cbOffsets:null,trigger:null});var n=o.data(a),i=n.opt,l=o.data("mcs-axis"),s=o.data("mcs-scrollbar-position"),c=o.data("mcs-theme");l&&(i.axis=l),s&&(i.scrollbarPosition=s),c&&(i.theme=c,h(i)),v.call(this),e("#mCSB_"+n.idx+"_container img:not(."+d[2]+")").addClass(d[2]),u.update.call(null,o)}})},update:function(t,o){var n=t||f.call(this);return e(n).each(function(){var t=e(this);if(t.data(a)){var n=t.data(a),i=n.opt,r=e("#mCSB_"+n.idx+"_container"),l=[e("#mCSB_"+n.idx+"_dragger_vertical"),e("#mCSB_"+n.idx+"_dragger_horizontal")];if(!r.length)return;n.tweenRunning&&V(t),t.hasClass(d[3])&&t.removeClass(d[3]),t.hasClass(d[4])&&t.removeClass(d[4]),S.call(this),_.call(this),"y"===i.axis||i.advanced.autoExpandHorizontalScroll||r.css("width",x(r.children())),n.overflowed=B.call(this),O.call(this),i.autoDraggerLength&&b.call(this),C.call(this),k.call(this);var s=[Math.abs(r[0].offsetTop),Math.abs(r[0].offsetLeft)];"x"!==i.axis&&(n.overflowed[0]?l[0].height()>l[0].parent().height()?T.call(this):(Q(t,s[0].toString(),{dir:"y",dur:0,overwrite:"none"}),n.contentReset.y=null):(T.call(this),"y"===i.axis?M.call(this):"yx"===i.axis&&n.overflowed[1]&&Q(t,s[1].toString(),{dir:"x",dur:0,overwrite:"none"}))),"y"!==i.axis&&(n.overflowed[1]?l[1].width()>l[1].parent().width()?T.call(this):(Q(t,s[1].toString(),{dir:"x",dur:0,overwrite:"none"}),n.contentReset.x=null):(T.call(this),"x"===i.axis?M.call(this):"yx"===i.axis&&n.overflowed[0]&&Q(t,s[0].toString(),{dir:"y",dur:0,overwrite:"none"}))),o&&n&&(2===o&&i.callbacks.onImageLoad&&"function"==typeof i.callbacks.onImageLoad?i.callbacks.onImageLoad.call(this):3===o&&i.callbacks.onSelectorChange&&"function"==typeof i.callbacks.onSelectorChange?i.callbacks.onSelectorChange.call(this):i.callbacks.onUpdate&&"function"==typeof i.callbacks.onUpdate&&i.callbacks.onUpdate.call(this)),X.call(this)}})},scrollTo:function(t,o){if("undefined"!=typeof t&&null!=t){var n=f.call(this);return e(n).each(function(){var n=e(this);if(n.data(a)){var i=n.data(a),r=i.opt,l={trigger:"external",scrollInertia:r.scrollInertia,scrollEasing:"mcsEaseInOut",moveDragger:!1,timeout:60,callbacks:!0,onStart:!0,onUpdate:!0,onComplete:!0},s=e.extend(!0,{},l,o),c=Y.call(this,t),d=s.scrollInertia>0&&s.scrollInertia<17?17:s.scrollInertia;c[0]=j.call(this,c[0],"y"),c[1]=j.call(this,c[1],"x"),s.moveDragger&&(c[0]*=i.scrollRatio.y,c[1]*=i.scrollRatio.x),s.dur=d,setTimeout(function(){null!==c[0]&&"undefined"!=typeof c[0]&&"x"!==r.axis&&i.overflowed[0]&&(s.dir="y",s.overwrite="all",Q(n,c[0].toString(),s)),null!==c[1]&&"undefined"!=typeof c[1]&&"y"!==r.axis&&i.overflowed[1]&&(s.dir="x",s.overwrite="none",Q(n,c[1].toString(),s))},s.timeout)}})}},stop:function(){var t=f.call(this);return e(t).each(function(){var t=e(this);t.data(a)&&V(t)})},disable:function(t){var o=f.call(this);return e(o).each(function(){var o=e(this);if(o.data(a)){{o.data(a)}X.call(this,"remove"),M.call(this),t&&T.call(this),O.call(this,!0),o.addClass(d[3])}})},destroy:function(){var t=f.call(this);return e(t).each(function(){var n=e(this);if(n.data(a)){var i=n.data(a),r=i.opt,l=e("#mCSB_"+i.idx),s=e("#mCSB_"+i.idx+"_container"),c=e(".mCSB_"+i.idx+"_scrollbar");r.live&&m(r.liveSelector||e(t).selector),X.call(this,"remove"),M.call(this),T.call(this),n.removeData(a),Z(this,"mcs"),c.remove(),s.find("img."+d[2]).removeClass(d[2]),l.replaceWith(s.contents()),n.removeClass(o+" _"+a+"_"+i.idx+" "+d[6]+" "+d[7]+" "+d[5]+" "+d[3]).addClass(d[4])}})}},f=function(){return"object"!=typeof e(this)||e(this).length<1?n:this},h=function(t){var o=["rounded","rounded-dark","rounded-dots","rounded-dots-dark"],a=["rounded-dots","rounded-dots-dark","3d","3d-dark","3d-thick","3d-thick-dark","inset","inset-dark","inset-2","inset-2-dark","inset-3","inset-3-dark"],n=["minimal","minimal-dark"],i=["minimal","minimal-dark"],r=["minimal","minimal-dark"];t.autoDraggerLength=e.inArray(t.theme,o)>-1?!1:t.autoDraggerLength,t.autoExpandScrollbar=e.inArray(t.theme,a)>-1?!1:t.autoExpandScrollbar,t.scrollButtons.enable=e.inArray(t.theme,n)>-1?!1:t.scrollButtons.enable,t.autoHideScrollbar=e.inArray(t.theme,i)>-1?!0:t.autoHideScrollbar,t.scrollbarPosition=e.inArray(t.theme,r)>-1?"outside":t.scrollbarPosition},m=function(e){l[e]&&(clearTimeout(l[e]),Z(l,e))},p=function(e){return"yx"===e||"xy"===e||"auto"===e?"yx":"x"===e||"horizontal"===e?"x":"y"},g=function(e){return"stepped"===e||"pixels"===e||"step"===e||"click"===e?"stepped":"stepless"},v=function(){var t=e(this),n=t.data(a),i=n.opt,r=i.autoExpandScrollbar?" "+d[1]+"_expand":"",l=["<div id='mCSB_"+n.idx+"_scrollbar_vertical' class='mCSB_scrollTools mCSB_"+n.idx+"_scrollbar mCS-"+i.theme+" mCSB_scrollTools_vertical"+r+"'><div class='"+d[12]+"'><div id='mCSB_"+n.idx+"_dragger_vertical' class='mCSB_dragger' style='position:absolute;' oncontextmenu='return false;'><div class='mCSB_dragger_bar' /></div><div class='mCSB_draggerRail' /></div></div>","<div id='mCSB_"+n.idx+"_scrollbar_horizontal' class='mCSB_scrollTools mCSB_"+n.idx+"_scrollbar mCS-"+i.theme+" mCSB_scrollTools_horizontal"+r+"'><div class='"+d[12]+"'><div id='mCSB_"+n.idx+"_dragger_horizontal' class='mCSB_dragger' style='position:absolute;' oncontextmenu='return false;'><div class='mCSB_dragger_bar' /></div><div class='mCSB_draggerRail' /></div></div>"],s="yx"===i.axis?"mCSB_vertical_horizontal":"x"===i.axis?"mCSB_horizontal":"mCSB_vertical",c="yx"===i.axis?l[0]+l[1]:"x"===i.axis?l[1]:l[0],u="yx"===i.axis?"<div id='mCSB_"+n.idx+"_container_wrapper' class='mCSB_container_wrapper' />":"",f=i.autoHideScrollbar?" "+d[6]:"",h="x"!==i.axis&&"rtl"===n.langDir?" "+d[7]:"";i.setWidth&&t.css("width",i.setWidth),i.setHeight&&t.css("height",i.setHeight),i.setLeft="y"!==i.axis&&"rtl"===n.langDir?"989999px":i.setLeft,t.addClass(o+" _"+a+"_"+n.idx+f+h).wrapInner("<div id='mCSB_"+n.idx+"' class='mCustomScrollBox mCS-"+i.theme+" "+s+"'><div id='mCSB_"+n.idx+"_container' class='mCSB_container' style='position:relative; top:"+i.setTop+"; left:"+i.setLeft+";' dir="+n.langDir+" /></div>");var m=e("#mCSB_"+n.idx),p=e("#mCSB_"+n.idx+"_container");"y"===i.axis||i.advanced.autoExpandHorizontalScroll||p.css("width",x(p.children())),"outside"===i.scrollbarPosition?("static"===t.css("position")&&t.css("position","relative"),t.css("overflow","visible"),m.addClass("mCSB_outside").after(c)):(m.addClass("mCSB_inside").append(c),p.wrap(u)),w.call(this);var g=[e("#mCSB_"+n.idx+"_dragger_vertical"),e("#mCSB_"+n.idx+"_dragger_horizontal")];g[0].css("min-height",g[0].height()),g[1].css("min-width",g[1].width())},x=function(t){return Math.max.apply(Math,t.map(function(){return e(this).outerWidth(!0)}).get())},_=function(){var t=e(this),o=t.data(a),n=o.opt,i=e("#mCSB_"+o.idx+"_container");n.advanced.autoExpandHorizontalScroll&&"y"!==n.axis&&i.css({position:"absolute",width:"auto"}).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({width:Math.ceil(i[0].getBoundingClientRect().right+.4)-Math.floor(i[0].getBoundingClientRect().left),position:"relative"}).unwrap()},w=function(){var t=e(this),o=t.data(a),n=o.opt,i=e(".mCSB_"+o.idx+"_scrollbar:first"),r=te(n.scrollButtons.tabindex)?"tabindex='"+n.scrollButtons.tabindex+"'":"",l=["<a href='#' class='"+d[13]+"' oncontextmenu='return false;' "+r+" />","<a href='#' class='"+d[14]+"' oncontextmenu='return false;' "+r+" />","<a href='#' class='"+d[15]+"' oncontextmenu='return false;' "+r+" />","<a href='#' class='"+d[16]+"' oncontextmenu='return false;' "+r+" />"],s=["x"===n.axis?l[2]:l[0],"x"===n.axis?l[3]:l[1],l[2],l[3]];n.scrollButtons.enable&&i.prepend(s[0]).append(s[1]).next(".mCSB_scrollTools").prepend(s[2]).append(s[3])},S=function(){var t=e(this),o=t.data(a),n=e("#mCSB_"+o.idx),i=t.css("max-height")||"none",r=-1!==i.indexOf("%"),l=t.css("box-sizing");if("none"!==i){var s=r?t.parent().height()*parseInt(i)/100:parseInt(i);"border-box"===l&&(s-=t.innerHeight()-t.height()+(t.outerHeight()-t.innerHeight())),n.css("max-height",Math.round(s))}},b=function(){var t=e(this),o=t.data(a),n=e("#mCSB_"+o.idx),i=e("#mCSB_"+o.idx+"_container"),r=[e("#mCSB_"+o.idx+"_dragger_vertical"),e("#mCSB_"+o.idx+"_dragger_horizontal")],l=[n.height()/i.outerHeight(!1),n.width()/i.outerWidth(!1)],c=[parseInt(r[0].css("min-height")),Math.round(l[0]*r[0].parent().height()),parseInt(r[1].css("min-width")),Math.round(l[1]*r[1].parent().width())],d=s&&c[1]<c[0]?c[0]:c[1],u=s&&c[3]<c[2]?c[2]:c[3];r[0].css({height:d,"max-height":r[0].parent().height()-10}).find(".mCSB_dragger_bar").css({"line-height":c[0]+"px"}),r[1].css({width:u,"max-width":r[1].parent().width()-10})},C=function(){var t=e(this),o=t.data(a),n=e("#mCSB_"+o.idx),i=e("#mCSB_"+o.idx+"_container"),r=[e("#mCSB_"+o.idx+"_dragger_vertical"),e("#mCSB_"+o.idx+"_dragger_horizontal")],l=[i.outerHeight(!1)-n.height(),i.outerWidth(!1)-n.width()],s=[l[0]/(r[0].parent().height()-r[0].height()),l[1]/(r[1].parent().width()-r[1].width())];o.scrollRatio={y:s[0],x:s[1]}},y=function(e,t,o){var a=o?d[0]+"_expanded":"",n=e.closest(".mCSB_scrollTools");"active"===t?(e.toggleClass(d[0]+" "+a),n.toggleClass(d[1]),e[0]._draggable=e[0]._draggable?0:1):e[0]._draggable||("hide"===t?(e.removeClass(d[0]),n.removeClass(d[1])):(e.addClass(d[0]),n.addClass(d[1])))},B=function(){var t=e(this),o=t.data(a),n=e("#mCSB_"+o.idx),i=e("#mCSB_"+o.idx+"_container"),r=null==o.overflowed?i.height():i.outerHeight(!1),l=null==o.overflowed?i.width():i.outerWidth(!1);return[r>n.height(),l>n.width()]},T=function(){var t=e(this),o=t.data(a),n=o.opt,i=e("#mCSB_"+o.idx),r=e("#mCSB_"+o.idx+"_container"),l=[e("#mCSB_"+o.idx+"_dragger_vertical"),e("#mCSB_"+o.idx+"_dragger_horizontal")];if(V(t),("x"!==n.axis&&!o.overflowed[0]||"y"===n.axis&&o.overflowed[0])&&(l[0].add(r).css("top",0),Q(t,"_resetY")),"y"!==n.axis&&!o.overflowed[1]||"x"===n.axis&&o.overflowed[1]){var s=dx=0;"rtl"===o.langDir&&(s=i.width()-r.outerWidth(!1),dx=Math.abs(s/o.scrollRatio.x)),r.css("left",s),l[1].css("left",dx),Q(t,"_resetX")}},k=function(){function t(){r=setTimeout(function(){e.event.special.mousewheel?(clearTimeout(r),W.call(o[0])):t()},100)}var o=e(this),n=o.data(a),i=n.opt;if(!n.bindEvents){if(R.call(this),i.contentTouchScroll&&D.call(this),E.call(this),i.mouseWheel.enable){var r;t()}P.call(this),H.call(this),i.advanced.autoScrollOnFocus&&z.call(this),i.scrollButtons.enable&&U.call(this),i.keyboard.enable&&F.call(this),n.bindEvents=!0}},M=function(){var t=e(this),o=t.data(a),n=o.opt,i=a+"_"+o.idx,r=".mCSB_"+o.idx+"_scrollbar",l=e("#mCSB_"+o.idx+",#mCSB_"+o.idx+"_container,#mCSB_"+o.idx+"_container_wrapper,"+r+" ."+d[12]+",#mCSB_"+o.idx+"_dragger_vertical,#mCSB_"+o.idx+"_dragger_horizontal,"+r+">a"),s=e("#mCSB_"+o.idx+"_container");n.advanced.releaseDraggableSelectors&&l.add(e(n.advanced.releaseDraggableSelectors)),o.bindEvents&&(e(document).unbind("."+i),l.each(function(){e(this).unbind("."+i)}),clearTimeout(t[0]._focusTimeout),Z(t[0],"_focusTimeout"),clearTimeout(o.sequential.step),Z(o.sequential,"step"),clearTimeout(s[0].onCompleteTimeout),Z(s[0],"onCompleteTimeout"),o.bindEvents=!1)},O=function(t){var o=e(this),n=o.data(a),i=n.opt,r=e("#mCSB_"+n.idx+"_container_wrapper"),l=r.length?r:e("#mCSB_"+n.idx+"_container"),s=[e("#mCSB_"+n.idx+"_scrollbar_vertical"),e("#mCSB_"+n.idx+"_scrollbar_horizontal")],c=[s[0].find(".mCSB_dragger"),s[1].find(".mCSB_dragger")];"x"!==i.axis&&(n.overflowed[0]&&!t?(s[0].add(c[0]).add(s[0].children("a")).css("display","block"),l.removeClass(d[8]+" "+d[10])):(i.alwaysShowScrollbar?(2!==i.alwaysShowScrollbar&&c[0].css("display","none"),l.removeClass(d[10])):(s[0].css("display","none"),l.addClass(d[10])),l.addClass(d[8]))),"y"!==i.axis&&(n.overflowed[1]&&!t?(s[1].add(c[1]).add(s[1].children("a")).css("display","block"),l.removeClass(d[9]+" "+d[11])):(i.alwaysShowScrollbar?(2!==i.alwaysShowScrollbar&&c[1].css("display","none"),l.removeClass(d[11])):(s[1].css("display","none"),l.addClass(d[11])),l.addClass(d[9]))),n.overflowed[0]||n.overflowed[1]?o.removeClass(d[5]):o.addClass(d[5])},I=function(e){var t=e.type;switch(t){case"pointerdown":case"MSPointerDown":case"pointermove":case"MSPointerMove":case"pointerup":case"MSPointerUp":return e.target.ownerDocument!==document?[e.originalEvent.screenY,e.originalEvent.screenX,!1]:[e.originalEvent.pageY,e.originalEvent.pageX,!1];case"touchstart":case"touchmove":case"touchend":var o=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0],a=e.originalEvent.touches.length||e.originalEvent.changedTouches.length;return e.target.ownerDocument!==document?[o.screenY,o.screenX,a>1]:[o.pageY,o.pageX,a>1];default:return[e.pageY,e.pageX,!1]}},R=function(){function t(e){var t=m.find("iframe");if(t.length){var o=e?"auto":"none";t.css("pointer-events",o)}}function o(e,t,o,a){if(m[0].idleTimer=u.scrollInertia<233?250:0,n.attr("id")===h[1])var i="x",r=(n[0].offsetLeft-t+a)*d.scrollRatio.x;else var i="y",r=(n[0].offsetTop-e+o)*d.scrollRatio.y;Q(l,r.toString(),{dir:i,drag:!0})}var n,i,r,l=e(this),d=l.data(a),u=d.opt,f=a+"_"+d.idx,h=["mCSB_"+d.idx+"_dragger_vertical","mCSB_"+d.idx+"_dragger_horizontal"],m=e("#mCSB_"+d.idx+"_container"),p=e("#"+h[0]+",#"+h[1]),g=u.advanced.releaseDraggableSelectors?p.add(e(u.advanced.releaseDraggableSelectors)):p;p.bind("mousedown."+f+" touchstart."+f+" pointerdown."+f+" MSPointerDown."+f,function(o){if(o.stopImmediatePropagation(),o.preventDefault(),$(o)){c=!0,s&&(document.onselectstart=function(){return!1}),t(!1),V(l),n=e(this);var a=n.offset(),d=I(o)[0]-a.top,f=I(o)[1]-a.left,h=n.height()+a.top,m=n.width()+a.left;h>d&&d>0&&m>f&&f>0&&(i=d,r=f),y(n,"active",u.autoExpandScrollbar)}}).bind("touchmove."+f,function(e){e.stopImmediatePropagation(),e.preventDefault();var t=n.offset(),a=I(e)[0]-t.top,l=I(e)[1]-t.left;o(i,r,a,l)}),e(document).bind("mousemove."+f+" pointermove."+f+" MSPointerMove."+f,function(e){if(n){var t=n.offset(),a=I(e)[0]-t.top,l=I(e)[1]-t.left;if(i===a)return;o(i,r,a,l)}}).add(g).bind("mouseup."+f+" touchend."+f+" pointerup."+f+" MSPointerUp."+f,function(e){n&&(y(n,"active",u.autoExpandScrollbar),n=null),c=!1,s&&(document.onselectstart=null),t(!0)})},D=function(){function o(e){if(!ee(e)||c||I(e)[2])return void(t=0);t=1,S=0,b=0,C.removeClass("mCS_touch_action");var o=M.offset();d=I(e)[0]-o.top,u=I(e)[1]-o.left,A=[I(e)[0],I(e)[1]]}function n(e){if(ee(e)&&!c&&!I(e)[2]&&(e.stopImmediatePropagation(),!b||S)){p=J();var t=k.offset(),o=I(e)[0]-t.top,a=I(e)[1]-t.left,n="mcsLinearOut";if(R.push(o),D.push(a),A[2]=Math.abs(I(e)[0]-A[0]),A[3]=Math.abs(I(e)[1]-A[1]),y.overflowed[0])var i=O[0].parent().height()-O[0].height(),r=d-o>0&&o-d>-(i*y.scrollRatio.y)&&(2*A[3]<A[2]||"yx"===B.axis);if(y.overflowed[1])var l=O[1].parent().width()-O[1].width(),f=u-a>0&&a-u>-(l*y.scrollRatio.x)&&(2*A[2]<A[3]||"yx"===B.axis);r||f?(e.preventDefault(),S=1):(b=1,C.addClass("mCS_touch_action")),_="yx"===B.axis?[d-o,u-a]:"x"===B.axis?[null,u-a]:[d-o,null],M[0].idleTimer=250,y.overflowed[0]&&s(_[0],E,n,"y","all",!0),y.overflowed[1]&&s(_[1],E,n,"x",W,!0)}}function i(e){if(!ee(e)||c||I(e)[2])return void(t=0);t=1,e.stopImmediatePropagation(),V(C),m=J();var o=k.offset();f=I(e)[0]-o.top,h=I(e)[1]-o.left,R=[],D=[]}function r(e){if(ee(e)&&!c&&!I(e)[2]){e.stopImmediatePropagation(),S=0,b=0,g=J();var t=k.offset(),o=I(e)[0]-t.top,a=I(e)[1]-t.left;if(!(g-p>30)){x=1e3/(g-m);var n="mcsEaseOut",i=2.5>x,r=i?[R[R.length-2],D[D.length-2]]:[0,0];v=i?[o-r[0],a-r[1]]:[o-f,a-h];var d=[Math.abs(v[0]),Math.abs(v[1])];x=i?[Math.abs(v[0]/4),Math.abs(v[1]/4)]:[x,x];var u=[Math.abs(M[0].offsetTop)-v[0]*l(d[0]/x[0],x[0]),Math.abs(M[0].offsetLeft)-v[1]*l(d[1]/x[1],x[1])];_="yx"===B.axis?[u[0],u[1]]:"x"===B.axis?[null,u[1]]:[u[0],null],w=[4*d[0]+B.scrollInertia,4*d[1]+B.scrollInertia];var C=parseInt(B.contentTouchScroll)||0;_[0]=d[0]>C?_[0]:0,_[1]=d[1]>C?_[1]:0,y.overflowed[0]&&s(_[0],w[0],n,"y",W,!1),y.overflowed[1]&&s(_[1],w[1],n,"x",W,!1)}}}function l(e,t){var o=[1.5*t,2*t,t/1.5,t/2];return e>90?t>4?o[0]:o[3]:e>60?t>3?o[3]:o[2]:e>30?t>8?o[1]:t>6?o[0]:t>4?t:o[2]:t>8?t:o[3]}function s(e,t,o,a,n,i){e&&Q(C,e.toString(),{dur:t,scrollEasing:o,dir:a,overwrite:n,drag:i})}var d,u,f,h,m,p,g,v,x,_,w,S,b,C=e(this),y=C.data(a),B=y.opt,T=a+"_"+y.idx,k=e("#mCSB_"+y.idx),M=e("#mCSB_"+y.idx+"_container"),O=[e("#mCSB_"+y.idx+"_dragger_vertical"),e("#mCSB_"+y.idx+"_dragger_horizontal")],R=[],D=[],E=0,W="yx"===B.axis?"none":"all",A=[],P=M.find("iframe"),z=["touchstart."+T+" pointerdown."+T+" MSPointerDown."+T,"touchmove."+T+" pointermove."+T+" MSPointerMove."+T,"touchend."+T+" pointerup."+T+" MSPointerUp."+T];M.bind(z[0],function(e){o(e)}).bind(z[1],function(e){n(e)}),k.bind(z[0],function(e){i(e)}).bind(z[2],function(e){r(e)}),P.length&&P.each(function(){e(this).load(function(){L(this)&&e(this.contentDocument||this.contentWindow.document).bind(z[0],function(e){o(e),i(e)}).bind(z[1],function(e){n(e)}).bind(z[2],function(e){r(e)})})})},E=function(){function o(){return window.getSelection?window.getSelection().toString():document.selection&&"Control"!=document.selection.type?document.selection.createRange().text:0}function n(e,t,o){d.type=o&&i?"stepped":"stepless",d.scrollAmount=10,q(r,e,t,"mcsLinearOut",o?60:null)}var i,r=e(this),l=r.data(a),s=l.opt,d=l.sequential,u=a+"_"+l.idx,f=e("#mCSB_"+l.idx+"_container"),h=f.parent();f.bind("mousedown."+u,function(e){t||i||(i=1,c=!0)}).add(document).bind("mousemove."+u,function(e){if(!t&&i&&o()){var a=f.offset(),r=I(e)[0]-a.top+f[0].offsetTop,c=I(e)[1]-a.left+f[0].offsetLeft;r>0&&r<h.height()&&c>0&&c<h.width()?d.step&&n("off",null,"stepped"):("x"!==s.axis&&l.overflowed[0]&&(0>r?n("on",38):r>h.height()&&n("on",40)),"y"!==s.axis&&l.overflowed[1]&&(0>c?n("on",37):c>h.width()&&n("on",39)))}}).bind("mouseup."+u,function(e){t||(i&&(i=0,n("off",null)),c=!1)})},W=function(){function t(t,a){if(V(o),!A(o,t.target)){var r="auto"!==i.mouseWheel.deltaFactor?parseInt(i.mouseWheel.deltaFactor):s&&t.deltaFactor<100?100:t.deltaFactor||100;if("x"===i.axis||"x"===i.mouseWheel.axis)var d="x",u=[Math.round(r*n.scrollRatio.x),parseInt(i.mouseWheel.scrollAmount)],f="auto"!==i.mouseWheel.scrollAmount?u[1]:u[0]>=l.width()?.9*l.width():u[0],h=Math.abs(e("#mCSB_"+n.idx+"_container")[0].offsetLeft),m=c[1][0].offsetLeft,p=c[1].parent().width()-c[1].width(),g=t.deltaX||t.deltaY||a;else var d="y",u=[Math.round(r*n.scrollRatio.y),parseInt(i.mouseWheel.scrollAmount)],f="auto"!==i.mouseWheel.scrollAmount?u[1]:u[0]>=l.height()?.9*l.height():u[0],h=Math.abs(e("#mCSB_"+n.idx+"_container")[0].offsetTop),m=c[0][0].offsetTop,p=c[0].parent().height()-c[0].height(),g=t.deltaY||a;"y"===d&&!n.overflowed[0]||"x"===d&&!n.overflowed[1]||((i.mouseWheel.invert||t.webkitDirectionInvertedFromDevice)&&(g=-g),i.mouseWheel.normalizeDelta&&(g=0>g?-1:1),(g>0&&0!==m||0>g&&m!==p||i.mouseWheel.preventDefault)&&(t.stopImmediatePropagation(),t.preventDefault()),Q(o,(h-g*f).toString(),{dir:d}))}}if(e(this).data(a)){var o=e(this),n=o.data(a),i=n.opt,r=a+"_"+n.idx,l=e("#mCSB_"+n.idx),c=[e("#mCSB_"+n.idx+"_dragger_vertical"),e("#mCSB_"+n.idx+"_dragger_horizontal")],d=e("#mCSB_"+n.idx+"_container").find("iframe");d.length&&d.each(function(){e(this).load(function(){L(this)&&e(this.contentDocument||this.contentWindow.document).bind("mousewheel."+r,function(e,o){t(e,o)})})}),l.bind("mousewheel."+r,function(e,o){t(e,o)})}},L=function(e){var t=null;try{var o=e.contentDocument||e.contentWindow.document;t=o.body.innerHTML}catch(a){}return null!==t},A=function(t,o){var n=o.nodeName.toLowerCase(),i=t.data(a).opt.mouseWheel.disableOver,r=["select","textarea"];return e.inArray(n,i)>-1&&!(e.inArray(n,r)>-1&&!e(o).is(":focus"))},P=function(){var t=e(this),o=t.data(a),n=a+"_"+o.idx,i=e("#mCSB_"+o.idx+"_container"),r=i.parent(),l=e(".mCSB_"+o.idx+"_scrollbar ."+d[12]);l.bind("touchstart."+n+" pointerdown."+n+" MSPointerDown."+n,function(e){c=!0}).bind("touchend."+n+" pointerup."+n+" MSPointerUp."+n,function(e){c=!1}).bind("click."+n,function(a){if(e(a.target).hasClass(d[12])||e(a.target).hasClass("mCSB_draggerRail")){V(t);var n=e(this),l=n.find(".mCSB_dragger");if(n.parent(".mCSB_scrollTools_horizontal").length>0){if(!o.overflowed[1])return;var s="x",c=a.pageX>l.offset().left?-1:1,u=Math.abs(i[0].offsetLeft)-.9*c*r.width()}else{if(!o.overflowed[0])return;var s="y",c=a.pageY>l.offset().top?-1:1,u=Math.abs(i[0].offsetTop)-.9*c*r.height()}Q(t,u.toString(),{dir:s,scrollEasing:"mcsEaseInOut"})}})},z=function(){var t=e(this),o=t.data(a),n=o.opt,i=a+"_"+o.idx,r=e("#mCSB_"+o.idx+"_container"),l=r.parent();r.bind("focusin."+i,function(o){var a=e(document.activeElement),i=r.find(".mCustomScrollBox").length,s=0;a.is(n.advanced.autoScrollOnFocus)&&(V(t),clearTimeout(t[0]._focusTimeout),t[0]._focusTimer=i?(s+17)*i:0,t[0]._focusTimeout=setTimeout(function(){var e=[oe(a)[0],oe(a)[1]],o=[r[0].offsetTop,r[0].offsetLeft],i=[o[0]+e[0]>=0&&o[0]+e[0]<l.height()-a.outerHeight(!1),o[1]+e[1]>=0&&o[0]+e[1]<l.width()-a.outerWidth(!1)],c="yx"!==n.axis||i[0]||i[1]?"all":"none";"x"===n.axis||i[0]||Q(t,e[0].toString(),{dir:"y",scrollEasing:"mcsEaseInOut",overwrite:c,dur:s}),"y"===n.axis||i[1]||Q(t,e[1].toString(),{dir:"x",scrollEasing:"mcsEaseInOut",overwrite:c,dur:s})},t[0]._focusTimer))})},H=function(){var t=e(this),o=t.data(a),n=a+"_"+o.idx,i=e("#mCSB_"+o.idx+"_container").parent();i.bind("scroll."+n,function(t){(0!==i.scrollTop()||0!==i.scrollLeft())&&e(".mCSB_"+o.idx+"_scrollbar").css("visibility","hidden")})},U=function(){var t=e(this),o=t.data(a),n=o.opt,i=o.sequential,r=a+"_"+o.idx,l=".mCSB_"+o.idx+"_scrollbar",s=e(l+">a");s.bind("mousedown."+r+" touchstart."+r+" pointerdown."+r+" MSPointerDown."+r+" mouseup."+r+" touchend."+r+" pointerup."+r+" MSPointerUp."+r+" mouseout."+r+" pointerout."+r+" MSPointerOut."+r+" click."+r,function(a){function r(e,o){i.scrollAmount=n.snapAmount||n.scrollButtons.scrollAmount,q(t,e,o)}if(a.preventDefault(),$(a)){var l=e(this).attr("class");switch(i.type=n.scrollButtons.scrollType,a.type){case"mousedown":case"touchstart":case"pointerdown":case"MSPointerDown":if("stepped"===i.type)return;c=!0,o.tweenRunning=!1,r("on",l);break;case"mouseup":case"touchend":case"pointerup":case"MSPointerUp":case"mouseout":case"pointerout":case"MSPointerOut":if("stepped"===i.type)return;c=!1,i.dir&&r("off",l);break;case"click":if("stepped"!==i.type||o.tweenRunning)return;r("on",l)}}})},F=function(){function t(t){function a(e,t){r.type=i.keyboard.scrollType,r.scrollAmount=i.snapAmount||i.keyboard.scrollAmount,"stepped"===r.type&&n.tweenRunning||q(o,e,t)}switch(t.type){case"blur":n.tweenRunning&&r.dir&&a("off",null);break;case"keydown":case"keyup":var l=t.keyCode?t.keyCode:t.which,s="on";if("x"!==i.axis&&(38===l||40===l)||"y"!==i.axis&&(37===l||39===l)){if((38===l||40===l)&&!n.overflowed[0]||(37===l||39===l)&&!n.overflowed[1])return;"keyup"===t.type&&(s="off"),e(document.activeElement).is(u)||(t.preventDefault(),t.stopImmediatePropagation(),a(s,l))}else if(33===l||34===l){if((n.overflowed[0]||n.overflowed[1])&&(t.preventDefault(),t.stopImmediatePropagation()),"keyup"===t.type){V(o);var f=34===l?-1:1;if("x"===i.axis||"yx"===i.axis&&n.overflowed[1]&&!n.overflowed[0])var h="x",m=Math.abs(c[0].offsetLeft)-.9*f*d.width();else var h="y",m=Math.abs(c[0].offsetTop)-.9*f*d.height();Q(o,m.toString(),{dir:h,scrollEasing:"mcsEaseInOut"})}}else if((35===l||36===l)&&!e(document.activeElement).is(u)&&((n.overflowed[0]||n.overflowed[1])&&(t.preventDefault(),t.stopImmediatePropagation()),"keyup"===t.type)){if("x"===i.axis||"yx"===i.axis&&n.overflowed[1]&&!n.overflowed[0])var h="x",m=35===l?Math.abs(d.width()-c.outerWidth(!1)):0;else var h="y",m=35===l?Math.abs(d.height()-c.outerHeight(!1)):0;Q(o,m.toString(),{dir:h,scrollEasing:"mcsEaseInOut"})}}}var o=e(this),n=o.data(a),i=n.opt,r=n.sequential,l=a+"_"+n.idx,s=e("#mCSB_"+n.idx),c=e("#mCSB_"+n.idx+"_container"),d=c.parent(),u="input,textarea,select,datalist,keygen,[contenteditable='true']",f=c.find("iframe"),h=["blur."+l+" keydown."+l+" keyup."+l];f.length&&f.each(function(){e(this).load(function(){L(this)&&e(this.contentDocument||this.contentWindow.document).bind(h[0],function(e){t(e)})})}),s.attr("tabindex","0").bind(h[0],function(e){t(e)})},q=function(t,o,n,i,r){function l(e){var o="stepped"!==f.type,a=r?r:e?o?p/1.5:g:1e3/60,n=e?o?7.5:40:2.5,s=[Math.abs(h[0].offsetTop),Math.abs(h[0].offsetLeft)],d=[c.scrollRatio.y>10?10:c.scrollRatio.y,c.scrollRatio.x>10?10:c.scrollRatio.x],u="x"===f.dir[0]?s[1]+f.dir[1]*d[1]*n:s[0]+f.dir[1]*d[0]*n,m="x"===f.dir[0]?s[1]+f.dir[1]*parseInt(f.scrollAmount):s[0]+f.dir[1]*parseInt(f.scrollAmount),v="auto"!==f.scrollAmount?m:u,x=i?i:e?o?"mcsLinearOut":"mcsEaseInOut":"mcsLinear",_=e?!0:!1;return e&&17>a&&(v="x"===f.dir[0]?s[1]:s[0]),Q(t,v.toString(),{dir:f.dir[0],scrollEasing:x,dur:a,onComplete:_}),e?void(f.dir=!1):(clearTimeout(f.step),void(f.step=setTimeout(function(){l()},a)))}function s(){clearTimeout(f.step),Z(f,"step"),V(t)}var c=t.data(a),u=c.opt,f=c.sequential,h=e("#mCSB_"+c.idx+"_container"),m="stepped"===f.type?!0:!1,p=u.scrollInertia<26?26:u.scrollInertia,g=u.scrollInertia<1?17:u.scrollInertia;switch(o){case"on":if(f.dir=[n===d[16]||n===d[15]||39===n||37===n?"x":"y",n===d[13]||n===d[15]||38===n||37===n?-1:1],V(t),te(n)&&"stepped"===f.type)return;l(m);break;case"off":s(),(m||c.tweenRunning&&f.dir)&&l(!0)}},Y=function(t){var o=e(this).data(a).opt,n=[];return"function"==typeof t&&(t=t()),t instanceof Array?n=t.length>1?[t[0],t[1]]:"x"===o.axis?[null,t[0]]:[t[0],null]:(n[0]=t.y?t.y:t.x||"x"===o.axis?null:t,n[1]=t.x?t.x:t.y||"y"===o.axis?null:t),"function"==typeof n[0]&&(n[0]=n[0]()),"function"==typeof n[1]&&(n[1]=n[1]()),n},j=function(t,o){if(null!=t&&"undefined"!=typeof t){var n=e(this),i=n.data(a),r=i.opt,l=e("#mCSB_"+i.idx+"_container"),s=l.parent(),c=typeof t;o||(o="x"===r.axis?"x":"y");var d="x"===o?l.outerWidth(!1):l.outerHeight(!1),f="x"===o?l[0].offsetLeft:l[0].offsetTop,h="x"===o?"left":"top";switch(c){case"function":return t();case"object":var m=t.jquery?t:e(t);if(!m.length)return;return"x"===o?oe(m)[1]:oe(m)[0];case"string":case"number":if(te(t))return Math.abs(t);if(-1!==t.indexOf("%"))return Math.abs(d*parseInt(t)/100);if(-1!==t.indexOf("-="))return Math.abs(f-parseInt(t.split("-=")[1]));if(-1!==t.indexOf("+=")){var p=f+parseInt(t.split("+=")[1]);return p>=0?0:Math.abs(p)}if(-1!==t.indexOf("px")&&te(t.split("px")[0]))return Math.abs(t.split("px")[0]);if("top"===t||"left"===t)return 0;if("bottom"===t)return Math.abs(s.height()-l.outerHeight(!1));if("right"===t)return Math.abs(s.width()-l.outerWidth(!1));if("first"===t||"last"===t){var m=l.find(":"+t);return"x"===o?oe(m)[1]:oe(m)[0]}return e(t).length?"x"===o?oe(e(t))[1]:oe(e(t))[0]:(l.css(h,t),void u.update.call(null,n[0]))}}},X=function(t){function o(){return clearTimeout(h[0].autoUpdate),0===s.parents("html").length?void(s=null):void(h[0].autoUpdate=setTimeout(function(){return f.advanced.updateOnSelectorChange&&(m=r(),m!==w)?(l(3),void(w=m)):(f.advanced.updateOnContentResize&&(p=[h.outerHeight(!1),h.outerWidth(!1),v.height(),v.width(),_()[0],_()[1]],(p[0]!==S[0]||p[1]!==S[1]||p[2]!==S[2]||p[3]!==S[3]||p[4]!==S[4]||p[5]!==S[5])&&(l(p[0]!==S[0]||p[1]!==S[1]),S=p)),f.advanced.updateOnImageLoad&&(g=n(),g!==b&&(h.find("img").each(function(){i(this)}),b=g)),void((f.advanced.updateOnSelectorChange||f.advanced.updateOnContentResize||f.advanced.updateOnImageLoad)&&o()))},f.advanced.autoUpdateTimeout))}function n(){var e=0;return f.advanced.updateOnImageLoad&&(e=h.find("img").length),e}function i(t){function o(e,t){return function(){return t.apply(e,arguments)}}function a(){this.onload=null,e(t).addClass(d[2]),l(2)}if(e(t).hasClass(d[2]))return void l();var n=new Image;n.onload=o(n,a),n.src=t.src}function r(){f.advanced.updateOnSelectorChange===!0&&(f.advanced.updateOnSelectorChange="*");var t=0,o=h.find(f.advanced.updateOnSelectorChange);return f.advanced.updateOnSelectorChange&&o.length>0&&o.each(function(){t+=e(this).height()+e(this).width()}),t}function l(e){clearTimeout(h[0].autoUpdate),u.update.call(null,s[0],e)}var s=e(this),c=s.data(a),f=c.opt,h=e("#mCSB_"+c.idx+"_container");if(t)return clearTimeout(h[0].autoUpdate),void Z(h[0],"autoUpdate");var m,p,g,v=h.parent(),x=[e("#mCSB_"+c.idx+"_scrollbar_vertical"),e("#mCSB_"+c.idx+"_scrollbar_horizontal")],_=function(){return[x[0].is(":visible")?x[0].outerHeight(!0):0,x[1].is(":visible")?x[1].outerWidth(!0):0]},w=r(),S=[h.outerHeight(!1),h.outerWidth(!1),v.height(),v.width(),_()[0],_()[1]],b=n();o()},N=function(e,t,o){return Math.round(e/t)*t-o},V=function(t){var o=t.data(a),n=e("#mCSB_"+o.idx+"_container,#mCSB_"+o.idx+"_container_wrapper,#mCSB_"+o.idx+"_dragger_vertical,#mCSB_"+o.idx+"_dragger_horizontal");n.each(function(){K.call(this)})},Q=function(t,o,n){function i(e){return s&&c.callbacks[e]&&"function"==typeof c.callbacks[e]}function r(){return[c.callbacks.alwaysTriggerOffsets||_>=w[0]+b,c.callbacks.alwaysTriggerOffsets||-C>=_]}function l(){var e=[h[0].offsetTop,h[0].offsetLeft],o=[v[0].offsetTop,v[0].offsetLeft],a=[h.outerHeight(!1),h.outerWidth(!1)],i=[f.height(),f.width()];t[0].mcs={content:h,top:e[0],left:e[1],draggerTop:o[0],draggerLeft:o[1],topPct:Math.round(100*Math.abs(e[0])/(Math.abs(a[0])-i[0])),leftPct:Math.round(100*Math.abs(e[1])/(Math.abs(a[1])-i[1])),direction:n.dir}}var s=t.data(a),c=s.opt,d={trigger:"internal",dir:"y",scrollEasing:"mcsEaseOut",drag:!1,dur:c.scrollInertia,overwrite:"all",
callbacks:!0,onStart:!0,onUpdate:!0,onComplete:!0},n=e.extend(d,n),u=[n.dur,n.drag?0:n.dur],f=e("#mCSB_"+s.idx),h=e("#mCSB_"+s.idx+"_container"),m=h.parent(),p=c.callbacks.onTotalScrollOffset?Y.call(t,c.callbacks.onTotalScrollOffset):[0,0],g=c.callbacks.onTotalScrollBackOffset?Y.call(t,c.callbacks.onTotalScrollBackOffset):[0,0];if(s.trigger=n.trigger,(0!==m.scrollTop()||0!==m.scrollLeft())&&(e(".mCSB_"+s.idx+"_scrollbar").css("visibility","visible"),m.scrollTop(0).scrollLeft(0)),"_resetY"!==o||s.contentReset.y||(i("onOverflowYNone")&&c.callbacks.onOverflowYNone.call(t[0]),s.contentReset.y=1),"_resetX"!==o||s.contentReset.x||(i("onOverflowXNone")&&c.callbacks.onOverflowXNone.call(t[0]),s.contentReset.x=1),"_resetY"!==o&&"_resetX"!==o){switch(!s.contentReset.y&&t[0].mcs||!s.overflowed[0]||(i("onOverflowY")&&c.callbacks.onOverflowY.call(t[0]),s.contentReset.x=null),!s.contentReset.x&&t[0].mcs||!s.overflowed[1]||(i("onOverflowX")&&c.callbacks.onOverflowX.call(t[0]),s.contentReset.x=null),c.snapAmount&&(o=N(o,c.snapAmount,c.snapOffset)),n.dir){case"x":var v=e("#mCSB_"+s.idx+"_dragger_horizontal"),x="left",_=h[0].offsetLeft,w=[f.width()-h.outerWidth(!1),v.parent().width()-v.width()],S=[o,0===o?0:o/s.scrollRatio.x],b=p[1],C=g[1],B=b>0?b/s.scrollRatio.x:0,T=C>0?C/s.scrollRatio.x:0;break;case"y":var v=e("#mCSB_"+s.idx+"_dragger_vertical"),x="top",_=h[0].offsetTop,w=[f.height()-h.outerHeight(!1),v.parent().height()-v.height()],S=[o,0===o?0:o/s.scrollRatio.y],b=p[0],C=g[0],B=b>0?b/s.scrollRatio.y:0,T=C>0?C/s.scrollRatio.y:0}S[1]<0||0===S[0]&&0===S[1]?S=[0,0]:S[1]>=w[1]?S=[w[0],w[1]]:S[0]=-S[0],t[0].mcs||(l(),i("onInit")&&c.callbacks.onInit.call(t[0])),clearTimeout(h[0].onCompleteTimeout),(s.tweenRunning||!(0===_&&S[0]>=0||_===w[0]&&S[0]<=w[0]))&&(G(v[0],x,Math.round(S[1]),u[1],n.scrollEasing),G(h[0],x,Math.round(S[0]),u[0],n.scrollEasing,n.overwrite,{onStart:function(){n.callbacks&&n.onStart&&!s.tweenRunning&&(i("onScrollStart")&&(l(),c.callbacks.onScrollStart.call(t[0])),s.tweenRunning=!0,y(v),s.cbOffsets=r())},onUpdate:function(){n.callbacks&&n.onUpdate&&i("whileScrolling")&&(l(),c.callbacks.whileScrolling.call(t[0]))},onComplete:function(){if(n.callbacks&&n.onComplete){"yx"===c.axis&&clearTimeout(h[0].onCompleteTimeout);var e=h[0].idleTimer||0;h[0].onCompleteTimeout=setTimeout(function(){i("onScroll")&&(l(),c.callbacks.onScroll.call(t[0])),i("onTotalScroll")&&S[1]>=w[1]-B&&s.cbOffsets[0]&&(l(),c.callbacks.onTotalScroll.call(t[0])),i("onTotalScrollBack")&&S[1]<=T&&s.cbOffsets[1]&&(l(),c.callbacks.onTotalScrollBack.call(t[0])),s.tweenRunning=!1,h[0].idleTimer=0,y(v,"hide")},e)}}}))}},G=function(e,t,o,a,n,i,r){function l(){S.stop||(x||m.call(),x=J()-v,s(),x>=S.time&&(S.time=x>S.time?x+f-(x-S.time):x+f-1,S.time<x+1&&(S.time=x+1)),S.time<a?S.id=h(l):g.call())}function s(){a>0?(S.currVal=u(S.time,_,b,a,n),w[t]=Math.round(S.currVal)+"px"):w[t]=o+"px",p.call()}function c(){f=1e3/60,S.time=x+f,h=window.requestAnimationFrame?window.requestAnimationFrame:function(e){return s(),setTimeout(e,.01)},S.id=h(l)}function d(){null!=S.id&&(window.requestAnimationFrame?window.cancelAnimationFrame(S.id):clearTimeout(S.id),S.id=null)}function u(e,t,o,a,n){switch(n){case"linear":case"mcsLinear":return o*e/a+t;case"mcsLinearOut":return e/=a,e--,o*Math.sqrt(1-e*e)+t;case"easeInOutSmooth":return e/=a/2,1>e?o/2*e*e+t:(e--,-o/2*(e*(e-2)-1)+t);case"easeInOutStrong":return e/=a/2,1>e?o/2*Math.pow(2,10*(e-1))+t:(e--,o/2*(-Math.pow(2,-10*e)+2)+t);case"easeInOut":case"mcsEaseInOut":return e/=a/2,1>e?o/2*e*e*e+t:(e-=2,o/2*(e*e*e+2)+t);case"easeOutSmooth":return e/=a,e--,-o*(e*e*e*e-1)+t;case"easeOutStrong":return o*(-Math.pow(2,-10*e/a)+1)+t;case"easeOut":case"mcsEaseOut":default:var i=(e/=a)*e,r=i*e;return t+o*(.499999999999997*r*i+-2.5*i*i+5.5*r+-6.5*i+4*e)}}e._mTween||(e._mTween={top:{},left:{}});var f,h,r=r||{},m=r.onStart||function(){},p=r.onUpdate||function(){},g=r.onComplete||function(){},v=J(),x=0,_=e.offsetTop,w=e.style,S=e._mTween[t];"left"===t&&(_=e.offsetLeft);var b=o-_;S.stop=0,"none"!==i&&d(),c()},J=function(){return window.performance&&window.performance.now?window.performance.now():window.performance&&window.performance.webkitNow?window.performance.webkitNow():Date.now?Date.now():(new Date).getTime()},K=function(){var e=this;e._mTween||(e._mTween={top:{},left:{}});for(var t=["top","left"],o=0;o<t.length;o++){var a=t[o];e._mTween[a].id&&(window.requestAnimationFrame?window.cancelAnimationFrame(e._mTween[a].id):clearTimeout(e._mTween[a].id),e._mTween[a].id=null,e._mTween[a].stop=1)}},Z=function(e,t){try{delete e[t]}catch(o){e[t]=null}},$=function(e){return!(e.which&&1!==e.which)},ee=function(e){var t=e.originalEvent.pointerType;return!(t&&"touch"!==t&&2!==t)},te=function(e){return!isNaN(parseFloat(e))&&isFinite(e)},oe=function(e){var t=e.parents(".mCSB_container");return[e.offset().top-t.offset().top,e.offset().left-t.offset().left]};e.fn[o]=function(t){return u[t]?u[t].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof t&&t?void e.error("Method "+t+" does not exist"):u.init.apply(this,arguments)},e[o]=function(t){return u[t]?u[t].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof t&&t?void e.error("Method "+t+" does not exist"):u.init.apply(this,arguments)},e[o].defaults=i,window[o]=!0,e(window).load(function(){e(n)[o](),e.extend(e.expr[":"],{mcsInView:e.expr[":"].mcsInView||function(t){var o,a,n=e(t),i=n.parents(".mCSB_container");if(i.length)return o=i.parent(),a=[i[0].offsetTop,i[0].offsetLeft],a[0]+oe(n)[0]>=0&&a[0]+oe(n)[0]<o.height()-n.outerHeight(!1)&&a[1]+oe(n)[1]>=0&&a[1]+oe(n)[1]<o.width()-n.outerWidth(!1)},mcsOverflow:e.expr[":"].mcsOverflow||function(t){var o=e(t).data(a);if(o)return o.overflowed[0]||o.overflowed[1]}})})})});
(function() {
    /**
     * Успешно разрешает Promise, если браузер поддерживает формат webP,
     * в противном случает отклоняет его
     *
     * @return Promise
     */
    function supportsWebp() {
        return new Promise(function(resolve, reject) {
            if (!self.createImageBitmap) {
                reject();
            } else {
                var webpData = 'data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAAAAAAfQ//73v/+BiOh/AAA=';

                fetch(webpData)
                    .then(function(response) {
                        return response.blob();
                    })
                    .then(function(blobData) {
                        return createImageBitmap(blobData).then(function() {
                            resolve();
                        }, function() {
                            reject();
                        });
                    }, function() {
                            reject();
                });
            }
        });
    }

    /**
     * Загружает скрипт и вызывает callback, по окончании загрузки
     *
     * @param url
     * @param callback
     * @return void
     */
    function loadScript(url, callback)
    {
        // Добавляем тег сценария в head
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;

        // Затем связываем событие и функцию обратного вызова.
        // Для поддержки большинства обозревателей используется несколько событий.
        script.onreadystatechange = callback;
        script.onload = callback;

        // Начинаем загрузку
        head.appendChild(script);
    }

    /**
     * Проверяет поддержку webP и в случае необходимости начинает загрузку polyfills для webP
     *
     * @return void
     */
    function start()
    {
        supportsWebp().catch(function() {

            var script = document.createElement('script');
            script.src = global.folder + '/resource/js/webpjs/polyfills.js';
            document.head.appendChild(script);

            var loadWebpJS = function() {
                document.addEventListener('DOMContentLoaded', function(){
                    var script = document.createElement('script');
                    script.src = global.folder + '/resource/js/webpjs/webp-init.js';
                    script.defer = true;
                    document.body.appendChild(script);
                });
            };
            loadScript(global.folder + "/resource/js/webpjs/webp-hero.bundle.js", loadWebpJS);
        });
    }

    /**
     * Проверяет, поддерживаются ли Promise в браузере, если нет, то загружает
     * сперва Polyfill для Promise и только потом запускает этап проверки webP
     *
     * @return void
     */
    function init()
    {
        if(/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) {
            //Если это IE, то загружаем polyfill для Promise
            loadScript(global.folder + "/resource/js/webpjs/bluebird.core.min.js", function() {
                start();
            });
        } else {
            start();
        }
    }

    init(); //Запускаем процесс
})();
/**
 * Обеспечивает корректную работу всплывающих окон телефонии
 *
 * @author ReadyScript lab.
 */
var telephony = {};

/**
 * Менеджер табов всплывающих окон для телефонии. Если одновременно всплывают
 * несколько окон, то они должны упаковываться в разные табы
 */
telephony.tabManager = {
    list: [],
    container: null,
    tabContainer: null,
    paneContainer: null,

    init: function() {
        var _this = this;
        this.container = $('<div class="tel-contnainer" id="tel-win-container">' +
                                '<a class="icon-circle tel-expand-button" title="' + lang.t('Развернуть') + '">' +
                                '    <i class="zmdi zmdi-phone-setting rubberBand animated infinite"></i>' +
                                '</a>' +
                            '</div>'
                           ).appendTo('body');

        if (global.telephonyOffsetBottom) {
            this.container.css('margin-bottom', global.telephonyOffsetBottom + 'px');
        }

        this.tabContainer = $('<ul class="tab-nav tel-tabs" role="tablist" data-tab-color="green"></ul>');
        this.paneContainer = $('<div class="tab-content tel-panes"></div>');

        this.tabContainer.on('click', 'a', function() {
            //Сохраняем активный таб
            $.cookie('crm-tel-active-tab', $(this).closest('[data-id]').data('id'), {
                expires:5000,
                path:'/'
            });
        });

        this.container
                .append(this.tabContainer, this.paneContainer)
                .on('click', '.tel-expand-button', function() {
                    _this.expand();
                });

        if ($.cookie('crm-tel-collapse-window')) {
            this.collapse();
        }

        this.paneContainer
                .on('click', '.tel-view-toggler', function() {
                    _this.collapse();
                })
                .on('click', '.close', function() {
                    var callId = $(this).closest('[data-id]').data('id');
                    _this.close(callId);
                })
                .on('click', '.tel-action', function() {
                    var callId = $(this).closest('[data-id]').data('id');
                    _this.doAction(callId, $(this).data('url'));
                });
    },

    /**
     * Восстанавливает активную вкладку
     */
    setActiveTab: function() {
        var tab = this.tabContainer.find('[data-id="' + $.cookie('crm-tel-active-tab') + '"] a');
        if (tab.length) {
            tab.click();
        } else {
            this.tabContainer.find(' > li:first > a').click();
        }
    },

    /**
     * Выполняет одно действие со звонком
     *
     * @param callId
     * @param url
     */
    doAction: function(callId, url) {
        var _this = this;

        $.ajaxQuery({
            loadingProgress: false,
            url: url,
            success:function(response) {
                if (!response.success) {
                    _this.showError(callId, response.error);
                }
            },
            error:function() {
                _this.showError(callId, lang.t('Не удалось выполнить запрос'));
            }
        })
    },

    /**
     * Отображает ошибку
     *
     * @param callId
     * @param error
     */
    showError: function(callId, error) {
        var errorContainer = this.paneContainer.find('[data-id="' + callId + '"] .tel-error');
        if (errorContainer.length) {
            errorContainer.text(error);
        }
        errorContainer.toggleClass('hidden', error == '');
    },

    /**
     * Добавляет новый таб со звонящим
     *
     * @param callerWin
     * @param noExpand
     */
    addTab: function(callerWin, noExpand, noSelectTab) {

        var existPane = this.paneContainer.find('[data-id="'+callerWin.id+'"]');
        var tab = this.tabContainer.find('[data-id="'+callerWin.id+'"]');
        var pane = this._renderTabContent(callerWin);

        if (existPane.length) {
            var isActive = existPane.hasClass('active');
            pane.toggleClass('active', isActive);
            existPane.replaceWith(pane);

        } else {
            pane.appendTo(this.paneContainer);

            var tab = this._renderTab(callerWin);
            tab.appendTo(this.tabContainer);

            this.list.push(callerWin);
            pane.trigger('new-content');
        }

        if (!noSelectTab) {
            tab.find('a').click(); //Активируем таб
        }

        this.updateActive();
        if (!noExpand) {
            this.expand();
        }
    },

    /**
     * Закрывает вкладку со звонком с пометкой
     *
     * @param id
     */
    close: function(id) {
        var url = this.paneContainer.find('[data-id="'+id+'"] .close').data('url');
        this.removeTab(id);
        $.get(url);
    },

    /**
     * Разворачивает окно с телефонией
     */
    expand: function() {
        this.container.removeClass('tel-collapsed');
        $.cookie('crm-tel-collapse-window', null, {
            path:'/'
        });
    },

    /**
     * Сворачивает окно с телефонией
     */
    collapse: function() {
        this.container.addClass('tel-collapsed');
        $.cookie('crm-tel-collapse-window', 1, {
            expires:5000,
            path:'/'
        });
    },

    /**
     * Закрывает вкладку. Переключает активную вкладку на другую
     *
     * @param id
     */
    removeTab: function(id) {
        var tab = this.tabContainer.find('[data-id="'+id+'"]');
        if (tab.is('.active')) {
            var nextActiveTab = tab.prev().length ? tab.prev() : tab.next();
            if (nextActiveTab.length) {
                nextActiveTab.find('> a').click(); //Меняем активный таб
            }
        }

        tab.remove();
        this.paneContainer.find('[data-id="'+id+'"]').remove();

        this.updateActive();
    },

    /**
     * Возвращает объект окна по ID
     *
     * @param id
     * @returns {boolean}
     */
    getCallerWinById: function(id) {
        this.list.forEach(function (callWin) {
            if (callWin.id == id) return callWin;
        });

        return false;
    },

    /**
     * Изменяет видимость блока телефонии, в зависимости от наличия активных табов
     * Скрывает табы, если звонков меньше 2х
     */
    updateActive: function() {
        var active = this.paneContainer.children().length > 0;
        this.container.toggleClass('active', active);

        var tabVisible = this.paneContainer.children().length > 1;
        this.tabContainer.toggleClass('hidden', !tabVisible);
    },

    /**
     * Возвращает jquery объект подготовленной вкладки (Таб)
     *
     * @param callerWin
     * @returns {jQuery|HTMLElement}
     */
    _renderTab: function(callerWin) {
        var n = this.list.length + 1;
        var li = $('<li>').attr('data-id', callerWin.id);
        var link = $('<a>').attr({
            href:'#tab-'+callerWin.id,
            role:'tab'
        }).attr('data-toggle', 'tab');

        link.text(callerWin.getTitle()).appendTo(li);

        return li;
    },

    /**
     * Возвращает jQuery объект подготовленного содержимого вкладки
     *
     * @param callerWin
     * @returns {jQuery|HTMLElement}
     */
    _renderTabContent: function(callerWin) {
        var pane = $('<div class="tab-pane tel-pane"/>');
        pane
            .attr('data-id', callerWin.id)
            .attr('id', 'tab-' + callerWin.id)
            .html(callerWin.getContentHtml());
        return pane;
    }
};

/**
 * Класс одного всплывающего окна
 */
telephony.callerWindow = function(id) {
    this.id = id;
    this.contentHtml = '';

    /**
     * Устанавливает HTML содержимого всплывающего окна
     *
     * @param html
     */
    this.setContentHtml = function(html) {
        this.contentHtml = html;
    };

    /**
     * Возвращает содержимое всплывающего окна
     *
     * @returns string
     */
    this.getContentHtml = function() {
        return this.contentHtml;
    };

    /**
     * Устанавливает заголовок вкладки окна
     *
     * @param title
     */
    this.setTitle = function(title) {
        this.title = title;
    };

    /**
     * Возвращает заголовок вкладки окна
     *
     * @returns string
     */
    this.getTitle = function() {
        return this.title;
    };
};

/**
 * Менеджер, управляющий отображением и обновлением
 * содержимого окон при наступлении различных событий
 */
telephony.eventManager = {

    init: function() {
        $('body').on('rs-event-crm.telephony.event', telephony.eventManager.onEvent);
        if (global.currentTelephonyMessages) {

            for(var key in global.currentTelephonyMessages) {
                this.onEvent(null, global.currentTelephonyMessages[key]); //Инициализируем текущие сообщения телефонии
            }

            telephony.tabManager.setActiveTab();
        }
    },

    onEvent: function(event, data) {
        var winId = data.id;
        if (winId) {
            if (data.closeCall) {

                telephony.tabManager.removeTab(winId);

            } else {

                var win = telephony.tabManager.getCallerWinById(winId);
                if (!win) {
                    win = new telephony.callerWindow(winId);
                }

                win.setContentHtml(data.html);
                win.setTitle(data.username);
                telephony.tabManager.addTab(win, event === null, event === null);
            }
        }
    }
};


/**
 * Инициализирует работу всплывающих окон телефонии
 */
$(function() {
    telephony.tabManager.init();
    telephony.eventManager.init();
});
//Инициализируем работу меню в админ. панели
$(window).load(function() {
    //Добавляем scrollbar'ы в меню
    $('.side-scroll').mCustomScrollbar({
        theme: 'minimal',
        scrollInertia: 0,
        mouseWheel:{ preventDefault: true }
    });

    $('.sm-body').mCustomScrollbar({
        theme: 'minimal-dark',
        autoHideScrollbar:true,
        scrollInertia: 0,
        mouseWheel:{ preventDefault: true }
    });

    $('body')
        .on($.rs.clickEventName, '#menu-trigger', function(e) {
            $(this).toggleClass('toggled');
            $('#sidebar').toggleClass('toggled');
            e.preventDefault();
        })
        .on($.rs.clickEventName, '.sm .sm-node > a', function(e) {
            $(this).parent().toggleClass('open');
            e.preventDefault();
        })
        .on($.rs.clickEventName, '.menu-close', function(e) {
            var self = this;
            $(this).closest('.sm-node').removeClass('open');
            $(this).closest('#sidebar').removeClass('sm-opened');
        });


        $('.side-menu > .sm-node > a')
            .on($.rs.clickEventName, function(e) {
                var parent = $(this).closest('.sm-node');
                var sidebar = $(this).closest('#sidebar');

                if (parent.is('.open')) {
                    parent.removeClass('open');
                    sidebar.removeClass('sm-opened');
                } else {
                    sidebar.find('.side-menu > .sm-node').removeClass('open');
                    parent.addClass('open');
                    sidebar.addClass('sm-opened');
                }
                e.preventDefault();
            })
            .on('dblclick', function() {
                if ($(this).data('url')) {
                    location.href = $(this).data('url');
                }
            });

        $('.side-menu-overlay').on($.rs.clickEventName, function() {
            $('.side-menu .sm-node').removeClass('open');
            $(this).closest('#sidebar').removeClass('sm-opened');
        });
});
/**
 * Класс позволяет выполнять запросы, которые возвращают результат в виде потока частиц
 */
class StreamFetcher
{
    constructor() {
        this.abortController = new AbortController;
    }

    /**
     * Возвращает контроллер, который может остановить выполнение запроса
     *
     * @returns {AbortController}
     */
    getAbortController()
    {
        return this.abortController;
    }

    /**
     * Устанавливает функцию, которая будет вызвана при получении каждой частицы ответа
     *
     * @param callback
     */
    setStreamCallback(callback)
    {
        this.streamCallback = callback;
        return this;
    }

    /**
     * Выполняет запрос на получение потога генеративных данных
     *
     * @param url
     * @param formData
     * @returns {Promise<any>}
     */
    async fetchStream(url, formData)
    {
        return new Promise(async (resolve, reject) => {
            let jsonData;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    signal: this.abortController.signal
                });

                if (response.headers.get('Content-type') === 'application/stream+json') {
                    //Обрабатываем поток данных
                    const reader = response.body.getReader();

                    //Запоминаем предыдущее значение для отмены ввода через CTRL+Z
                    const textDecoder = new TextDecoder();
                    let buffer = '';
                    let fullText = '';
                    let iteration = 0;

                    while (true) {
                        const {done, value} = await reader.read();
                        if (done) break;

                        // Декодируем бинарные данные в текст
                        buffer += textDecoder.decode(value, {stream: true});

                        // Разделяем буфер по \n и обрабатываем каждый JSON
                        const lines = buffer.split('\n');
                        // Оставляем на следующую итерацию, то, что было после последнего \n
                        buffer = lines.pop();

                        for (const line of lines) {
                            if (line.trim() === '') continue; // Пропускаем пустые строки
                            try {
                                jsonData = JSON.parse(line);
                                if (jsonData.balance) {
                                    //Сообщаем другим модулям о новом балансе
                                    window.dispatchEvent(new CustomEvent('gptBalanceChange', {
                                        bubbles:true,
                                        detail: {
                                            balance: jsonData.balance,
                                            source: 'button'
                                        }
                                    }));
                                }
                                fullText = fullText + jsonData.text;
                                if (this.streamCallback) {
                                    this.streamCallback(fullText, jsonData, iteration);
                                }
                                iteration++;
                            } catch (error) {
                                console.log(error);
                            }
                        }
                    }
                    resolve({fullText, lastChunk: jsonData});
                } else {
                    //Обрабатываем обычный не потоковый ответ
                    return response.json().then(jsonData => {
                        $.rs.checkAuthorization(jsonData);
                        $.rs.checkWindowRedirect(jsonData);
                        $.rs.checkMessages(jsonData);
                        if (typeof(jsonData) == 'object' && jsonData.messages) {
                            reject(jsonData.messages[0].text);
                        } else {
                            resolve(jsonData);
                        }
                    });
                }
            } catch(error) {
                if (!(/^AbortError/.test(error))) {
                    $.messenger('show', {
                        theme: 'error',
                        text: error
                    });
                }
                reject(error);
            }
        });
    }
};
/**
 * Jquery плагин, отвечающий за ИИ-кнопки возле форм
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.aiButton = function( method ) {
        const defaults = {},
            args = arguments;

        return this.map(function() {
            let $this = $(this),
                data = $this.data('aiButtonInstance'),
                params = $this.data('aiButton');

            const methods = {
                init: function (initOptions) {
                    if (data) return;
                    data = {};
                    $this.data('aiButtonInstance', data);
                    data.opt = $.extend({}, defaults, initOptions);

                    createButton();
                    bindAutoStop();
                },

                /**
                 * Выполняет запрос на генерацию значения для поля
                 *
                 * @param promptId
                 * @param force - всегда запускать
                 */
                startGeneration: async function(promptId, force) {
                    if (data.$buttonGroup.hasClass('loading')) {
                        methods.stopGeneration();
                        if (!force) return;
                    }

                    return new Promise(async (resolve, reject) => {
                        let form = data.$buttonGroup.closest('form');

                        if (!promptId) {
                            promptId = data.$mainButton.data('promptId');
                        }

                        data.$buttonGroup.addClass('loading');
                        let url = global.ai.generateUrl + '&prompt_id=' + promptId;
                        let formData = new FormData(form[0]);

                        data.fetcher = new StreamFetcher();
                        data.fetcher.setStreamCallback((fulltext, jsonData, iteration) => {
                            if (iteration === 0) {
                                data.previousValue = $this[0].value;
                                $this[0].value = '';
                            }

                            $this[0].value += jsonData.text;
                        });

                        data.fetcher.fetchStream(url, formData)
                            .then(object => {
                                resolve(object);
                            })
                            .catch(error => {
                                reject(error);
                            })
                            .finally(() => {
                                data.$buttonGroup.removeClass('loading');
                            });
                    });
                },

                /**
                 * Прерывает генерацию значения
                 */
                stopGeneration: function() {
                    if (data.fetcher) {
                        data.fetcher.getAbortController().abort('AbortError');
                    }
                }
            };

            //private
            const
            /**
             * Создает кнопку генерации текста с выпадающим списком
             */
            createButton = function() {
                if (!params['prompts'].length) return;

                let isMultiedit = $this.closest('.multi_edit_rightcol');
                if (isMultiedit.length) return;

                data.$buttonGroup = $(`<div class="btn-group ai-btn-group"></div>`);
                data.$mainButton = $(`<button type="button" class="btn btn-default ai-gen"></button>`)
                    .attr('data-prompt-id', params['prompts'][0]['id'])
                    .attr('title', lang.t('Заполнить через ИИ'))
                    .appendTo(data.$buttonGroup);

                data.$dropDownButton = $(`<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false"><span class="caret"></span>
                                            </button>`).appendTo(data.$buttonGroup);

                data.$dropDown = $(`<ul class="dropdown-menu dropdown-menu-right"></ul>`).appendTo(data.$buttonGroup);

                params['prompts'].forEach((prompt) => {
                    $('<li/>').append(
                        $('<a/>')
                            .attr('data-prompt-id', prompt['id'])
                            .text(prompt['note'])
                            .on('click', event => {
                                methods.startGeneration($(event.currentTarget).data('prompt-id')).catch(() => {});
                            })
                    ).appendTo(data.$dropDown);
                });

                data.$buttonGroup.data('aiBaseField', $this);
                $this
                    .wrap('<span class="form-wrapper"></span>')
                    .parent()
                    .append(data.$buttonGroup);

                data.$buttonGroup.parent().trigger('new-content');

                data.$mainButton.on('click', event => {
                    methods.startGeneration($(event.currentTarget).data('prompt-id')).catch(() => {});
                });
                $this.on('keyup', checkUndo);
            },

            /**
             * Позволяет возвращать значение в поле до генерации при нажатии CTRL+Z
             *
             * @param event
             */
            checkUndo = function(event) {
                if (data.previousValue !== null && event.ctrlKey && event.keyCode === 90) {
                    $this[0].value = data.previousValue;
                    data.previousValue = null;

                    event.preventDefault();
                }
            },

            /**
             * Автоматически прерывает запрос, если закрылось окно, в котором находится поле
             */
            bindAutoStop = function() {
                $this.closest('.ui-dialog').on('dialogclose', methods.stopGeneration);
            };

            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                methods.init.apply(this, args);
                return this;
            }
        });
    }

    //Инициализируем каждый раз, когда на странице появляется новый контент
    $.contentReady(function () {
        $('[data-ai-button]', this).aiButton();
    });

})(jQuery);
/**
 * Jquery плагин, отвечающий за генерацию контента для textarea, которые связаны с TinyMCE
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.aiRichText = function( method ) {
        const defaults = {},
            args = arguments;

        return this.map(function() {
            let $this = $(this),
                data = $this.data('aiRichTextInstance'),
                params = $this.data('aiRichtext');

            const methods = {
                init: function (initOptions) {
                    if (data) return;
                    data = {};
                    $this.data('aiRichTextInstance', data);
                    data.opt = $.extend({}, defaults, initOptions);
                    bindAutoStop();
                },

                /**
                 * Выполняет запрос на генерацию значения для поля
                 *
                 * @param promptId
                 * @param force - всегда запускать
                 */
                startGeneration: async function(promptId, force) {
                    if (data['aiLoading'] ===  true) {
                        methods.stopGeneration();
                        if (!force) return;
                    }

                    return new Promise(async (resolve, reject) => {
                        let form = $this.closest('form');

                        if (!promptId) {
                            promptId = params['prompts'][0]['id'];
                        }

                        //data.$buttonGroup.addClass('loading');
                        data['aiLoading'] = true;
                        $this.trigger('aiStartLoading');

                        let url = global.ai.generateUrl + '&prompt_id=' + promptId;
                        let formData = new FormData(form[0]);

                        data.fetcher = new StreamFetcher();
                        data.fetcher.setStreamCallback((fulltext, jsonData, iteration) => {
                                if (iteration === 0) {
                                    $this.trigger('aiBeforeFirstSetValue');
                                    $this[0].value = '';
                                }

                                $this[0].value += jsonData.text;
                                $this.trigger('aiSetValue', [$this[0].value]);
                            })

                        data.fetcher.fetchStream(url, formData)
                            .then(object => {
                                resolve(object);
                            })
                            .catch(error => {
                                reject(error);
                            })
                            .finally(() => {
                                data['aiLoading'] = null;
                                $this.trigger('aiEndLoading');
                            });
                    });
                },

                /**
                 * Прерывает генерацию значения
                 */
                stopGeneration: function() {
                    if (data.fetcher) {
                        data.fetcher.getAbortController().abort('AbortError');
                    }
                }
            };

            //private
            const

            /**
             * Автоматически прерывает запрос, если закрылось окно, в котором находится поле
             */
            bindAutoStop = function() {
                $this.closest('.ui-dialog').on('dialogclose', methods.stopGeneration);
            };

            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                methods.init.apply(this, args);
                return this;
            }
        });
    }

    //Инициализируем каждый раз, когда на странице появляется новый контент
    $.contentReady(function () {
        $('[data-ai-richtext]', this).aiRichText();
    });

})(jQuery);
/**
 * Jquery плагин, отвечающий за ИИ-кнопки возле главной формы в админке
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.aiMainButton = function( method ) {
        const defaults = {},
            args = arguments;

        return this.each(function() {
            let $this = $(this),
                data = $this.data('aiMainButtonInstance'),
                params;

            const methods = {
                init: function (initOptions) {
                    if (data) return;
                    data = {};
                    $this.data('aiMainButtonInstance', data);
                    data.opt = $.extend({}, defaults, initOptions);
                    params = $this.data('aiMainButton');

                    createButton();
                },
                /**
                 * Выполняет запрос на генерацию значения для поля
                 *
                 * @param promptId
                 */
                startGeneration: async function() {
                    if (data.$buttonGroup.hasClass('loading')) {
                        return methods.stopGeneration();
                    }

                    if ($this.val() === '') {
                        $.messenger('show', {
                            theme: 'error',
                            text: lang.t('Заполните поле `%field`, чтобы на основе него можно было сгенерировать остальные поля', {
                                field: params.main_field_title
                            })
                        });
                        return;
                    }

                    data.$buttonGroup.addClass('loading');

                    //Получаем список форм, для которых доступна генерация
                    let formsByField = getFormsByField();

                    //Последовательно запускаем эти формы
                    for(let n in params['generate_fields']) {
                        let promises = [];
                        for(let i in params['generate_fields'][n]) {
                            let field = params['generate_fields'][n][i];
                            if (formsByField[field].val() === '')
                            {
                                let promise;
                                if (formsByField[field].is('[data-ai-button]')) {
                                    promise = formsByField[field].aiButton('startGeneration', null, true).get(0);
                                }
                                if (formsByField[field].is('[data-ai-richtext]')) {
                                    let tinymce = $(formsByField[field]).tinymce();
                                    if (tinymce) {
                                        promise = tinymce.rs.startGeneration(tinymce.rs.aiGenerateApi, null, true);
                                    } else {
                                        //Если мы здесь, значит tinymce находится на отдельной вкладке и он не инициализирован
                                        //В этом случае мы запускаем генерацию для обычного
                                        promise = formsByField[field].aiRichText('startGeneration', null, true).get(0);
                                    }
                                }

                                promise.catch(() => {});

                                if (promise) {
                                    promises.push(promise);
                                }
                            }
                        }

                        try {
                            await Promise.all(promises);
                        } catch(error) {
                            break;
                        }
                    }

                    data.$buttonGroup.removeClass('loading');
                },

                /**
                 * Прерывает генерацию значения
                 */
                stopGeneration: function() {
                    data.$buttonGroup.removeClass('loading');

                    let form = data.$buttonGroup.closest('form');

                    //Получаем список форм, для которых доступна генерация
                    let formsByField = {};
                    $('[data-ai-button]', form)
                        .add($('[data-ai-richtext]', form))
                        .each((n, element) => {
                            let $element = $(element);
                            if ($element.is('[data-ai-button]')) {
                                $element.aiButton('stopGeneration')
                            }
                            if ($element.is('[data-ai-richtext]')) {
                                let tinymce = $element.tinymce();
                                tinymce.rs.stopGeneration();
                            }
                        });
                }
            };

            //private
            const
                /**
                 * Создает кнопку генерации текста с выпадающим списком
                 */
                createButton = function() {
                    let isMultiedit = $this.closest('.multi_edit_rightcol');
                    if (isMultiedit.length) return;
                    if (!Object.keys(getFormsByField()).length) return;

                    data.$buttonGroup = $(`<div class="btn-group ai-btn-group"></div>`);
                    data.$mainButton = $(`<button type="button" class="btn btn-default ai-gen main"></button>`)
                        .attr('title', lang.t('Заполнить пустые поля'))
                        .appendTo(data.$buttonGroup);

                    data.$buttonGroup.data('aiBaseField', $this);
                    $this
                        .wrap('<span class="form-wrapper"></span>')
                        .parent()
                        .append(data.$buttonGroup);
                    data.$buttonGroup.parent().trigger('new-content');

                    data.$mainButton.on('click', event => methods.startGeneration());
                },

                /**
                 * Возвращает список элементов, для которых доступна генерация
                 *
                 * @returns {{}}
                 */
                getFormsByField = function() {
                    let formsByField = {};

                    let form = $this.closest('form');
                    $('[data-ai-button]', form)
                        .add($('[data-ai-richtext]', form))
                        .each((n, element) => {
                            let $element = $(element);
                            if ($element.is('[data-ai-button]') && $element.data('aiButton').prompts.length) {
                                formsByField[$element.data('aiButton').field_name] = $element;
                            }
                            if ($element.is('[data-ai-richtext]') && $element.data('aiRichtext').prompts.length) {
                                formsByField[$element.data('aiRichtext').field_name] = $element;
                            }
                        });

                    return formsByField;
                }

            if (methods[method]) {
                methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, args);
            }
        });
    }

    //Инициализируем каждый раз, когда на странице появляется новый контент
    $.contentReady(function () {
        $('[data-ai-main-button]', this).aiMainButton();
    });

})(jQuery);
/**
 * AI Assistant ReadyScript v1.0.0
 * Author: ReadyScript lab.
 * License: https://readyscript.ru/licenseAgreement/
 */

function e(e){const t=Object.create(null);for(const n of e.split(","))t[n]=1;return e=>e in t}const t={},n=[],o=()=>{},i=()=>!1,s=e=>111===e.charCodeAt(0)&&110===e.charCodeAt(1)&&(e.charCodeAt(2)>122||e.charCodeAt(2)<97),r=e=>e.startsWith("onUpdate:"),l=Object.assign,a=(e,t)=>{const n=e.indexOf(t);n>-1&&e.splice(n,1)},c=Object.prototype.hasOwnProperty,u=(e,t)=>c.call(e,t),d=Array.isArray,p=e=>"[object Map]"===b(e),h=e=>"[object Set]"===b(e),f=e=>"function"==typeof e,g=e=>"string"==typeof e,m=e=>"symbol"==typeof e,v=e=>null!==e&&"object"==typeof e,y=e=>(v(e)||f(e))&&f(e.then)&&f(e.catch),w=Object.prototype.toString,b=e=>w.call(e),C=e=>"[object Object]"===b(e),x=e=>g(e)&&"NaN"!==e&&"-"!==e[0]&&""+parseInt(e,10)===e,S=e(",key,ref,ref_for,ref_key,onVnodeBeforeMount,onVnodeMounted,onVnodeBeforeUpdate,onVnodeUpdated,onVnodeBeforeUnmount,onVnodeUnmounted"),k=e=>{const t=Object.create(null);return n=>t[n]||(t[n]=e(n))},T=/-(\w)/g,L=k((e=>e.replace(T,((e,t)=>t?t.toUpperCase():"")))),P=/\B([A-Z])/g,O=k((e=>e.replace(P,"-$1").toLowerCase())),M=k((e=>e.charAt(0).toUpperCase()+e.slice(1))),A=k((e=>e?`on${M(e)}`:"")),E=(e,t)=>!Object.is(e,t),D=(e,...t)=>{for(let n=0;n<e.length;n++)e[n](...t)},R=(e,t,n,o=!1)=>{Object.defineProperty(e,t,{configurable:!0,enumerable:!1,writable:o,value:n})},H=e=>{const t=parseFloat(e);return isNaN(t)?e:t};let j;const z=()=>j||(j="undefined"!=typeof globalThis?globalThis:"undefined"!=typeof self?self:"undefined"!=typeof window?window:"undefined"!=typeof global?global:{});function B(e){if(d(e)){const t={};for(let n=0;n<e.length;n++){const o=e[n],i=g(o)?I(o):B(o);if(i)for(const e in i)t[e]=i[e]}return t}if(g(e)||v(e))return e}const N=/;(?![^(]*\))/g,F=/:([^]+)/,V=/\/\*[^]*?\*\//g;function I(e){const t={};return e.replace(V,"").split(N).forEach((e=>{if(e){const n=e.split(F);n.length>1&&(t[n[0].trim()]=n[1].trim())}})),t}function U(e){let t="";if(g(e))t=e;else if(d(e))for(let n=0;n<e.length;n++){const o=U(e[n]);o&&(t+=o+" ")}else if(v(e))for(const n in e)e[n]&&(t+=n+" ");return t.trim()}function W(e){if(!e)return null;let{class:t,style:n}=e;return t&&!g(t)&&(e.class=U(t)),n&&(e.style=B(n)),e}const Z=e("itemscope,allowfullscreen,formnovalidate,ismap,nomodule,novalidate,readonly");function q(e){return!!e||""===e}const G=e=>!(!e||!0!==e.__v_isRef),Y=e=>g(e)?e:null==e?"":d(e)||v(e)&&(e.toString===w||!f(e.toString))?G(e)?Y(e.value):JSON.stringify(e,K,2):String(e),K=(e,t)=>G(t)?K(e,t.value):p(t)?{[`Map(${t.size})`]:[...t.entries()].reduce(((e,[t,n],o)=>(e[X(t,o)+" =>"]=n,e)),{})}:h(t)?{[`Set(${t.size})`]:[...t.values()].map((e=>X(e)))}:m(t)?X(t):!v(t)||d(t)||C(t)?t:String(t),X=(e,t="")=>{var n;return m(e)?`Symbol(${null!=(n=e.description)?n:t})`:e};let Q,J;class ee{constructor(e=!1){this.detached=e,this._active=!0,this._on=0,this.effects=[],this.cleanups=[],this._isPaused=!1,this.parent=Q,!e&&Q&&(this.index=(Q.scopes||(Q.scopes=[])).push(this)-1)}get active(){return this._active}pause(){if(this._active){let e,t;if(this._isPaused=!0,this.scopes)for(e=0,t=this.scopes.length;e<t;e++)this.scopes[e].pause();for(e=0,t=this.effects.length;e<t;e++)this.effects[e].pause()}}resume(){if(this._active&&this._isPaused){let e,t;if(this._isPaused=!1,this.scopes)for(e=0,t=this.scopes.length;e<t;e++)this.scopes[e].resume();for(e=0,t=this.effects.length;e<t;e++)this.effects[e].resume()}}run(e){if(this._active){const t=Q;try{return Q=this,e()}finally{Q=t}}}on(){1===++this._on&&(this.prevScope=Q,Q=this)}off(){this._on>0&&0===--this._on&&(Q=this.prevScope,this.prevScope=void 0)}stop(e){if(this._active){let t,n;for(this._active=!1,t=0,n=this.effects.length;t<n;t++)this.effects[t].stop();for(this.effects.length=0,t=0,n=this.cleanups.length;t<n;t++)this.cleanups[t]();if(this.cleanups.length=0,this.scopes){for(t=0,n=this.scopes.length;t<n;t++)this.scopes[t].stop(!0);this.scopes.length=0}if(!this.detached&&this.parent&&!e){const e=this.parent.scopes.pop();e&&e!==this&&(this.parent.scopes[this.index]=e,e.index=this.index)}this.parent=void 0}}}const te=new WeakSet;class ne{constructor(e){this.fn=e,this.deps=void 0,this.depsTail=void 0,this.flags=5,this.next=void 0,this.cleanup=void 0,this.scheduler=void 0,Q&&Q.active&&Q.effects.push(this)}pause(){this.flags|=64}resume(){64&this.flags&&(this.flags&=-65,te.has(this)&&(te.delete(this),this.trigger()))}notify(){2&this.flags&&!(32&this.flags)||8&this.flags||re(this)}run(){if(!(1&this.flags))return this.fn();this.flags|=2,we(this),ce(this);const e=J,t=ge;J=this,ge=!0;try{return this.fn()}finally{ue(this),J=e,ge=t,this.flags&=-3}}stop(){if(1&this.flags){for(let e=this.deps;e;e=e.nextDep)he(e);this.deps=this.depsTail=void 0,we(this),this.onStop&&this.onStop(),this.flags&=-2}}trigger(){64&this.flags?te.add(this):this.scheduler?this.scheduler():this.runIfDirty()}runIfDirty(){de(this)&&this.run()}get dirty(){return de(this)}}let oe,ie,se=0;function re(e,t=!1){if(e.flags|=8,t)return e.next=ie,void(ie=e);e.next=oe,oe=e}function le(){se++}function ae(){if(--se>0)return;if(ie){let e=ie;for(ie=void 0;e;){const t=e.next;e.next=void 0,e.flags&=-9,e=t}}let e;for(;oe;){let n=oe;for(oe=void 0;n;){const o=n.next;if(n.next=void 0,n.flags&=-9,1&n.flags)try{n.trigger()}catch(t){e||(e=t)}n=o}}if(e)throw e}function ce(e){for(let t=e.deps;t;t=t.nextDep)t.version=-1,t.prevActiveLink=t.dep.activeLink,t.dep.activeLink=t}function ue(e){let t,n=e.depsTail,o=n;for(;o;){const e=o.prevDep;-1===o.version?(o===n&&(n=e),he(o),fe(o)):t=o,o.dep.activeLink=o.prevActiveLink,o.prevActiveLink=void 0,o=e}e.deps=t,e.depsTail=n}function de(e){for(let t=e.deps;t;t=t.nextDep)if(t.dep.version!==t.version||t.dep.computed&&(pe(t.dep.computed)||t.dep.version!==t.version))return!0;return!!e._dirty}function pe(e){if(4&e.flags&&!(16&e.flags))return;if(e.flags&=-17,e.globalVersion===be)return;if(e.globalVersion=be,!e.isSSR&&128&e.flags&&(!e.deps&&!e._dirty||!de(e)))return;e.flags|=2;const t=e.dep,n=J,o=ge;J=e,ge=!0;try{ce(e);const n=e.fn(e._value);(0===t.version||E(n,e._value))&&(e.flags|=128,e._value=n,t.version++)}catch(i){throw t.version++,i}finally{J=n,ge=o,ue(e),e.flags&=-3}}function he(e,t=!1){const{dep:n,prevSub:o,nextSub:i}=e;if(o&&(o.nextSub=i,e.prevSub=void 0),i&&(i.prevSub=o,e.nextSub=void 0),n.subs===e&&(n.subs=o,!o&&n.computed)){n.computed.flags&=-5;for(let e=n.computed.deps;e;e=e.nextDep)he(e,!0)}t||--n.sc||!n.map||n.map.delete(n.key)}function fe(e){const{prevDep:t,nextDep:n}=e;t&&(t.nextDep=n,e.prevDep=void 0),n&&(n.prevDep=t,e.nextDep=void 0)}let ge=!0;const me=[];function ve(){me.push(ge),ge=!1}function ye(){const e=me.pop();ge=void 0===e||e}function we(e){const{cleanup:t}=e;if(e.cleanup=void 0,t){const e=J;J=void 0;try{t()}finally{J=e}}}let be=0;class _e{constructor(e,t){this.sub=e,this.dep=t,this.version=t.version,this.nextDep=this.prevDep=this.nextSub=this.prevSub=this.prevActiveLink=void 0}}class Ce{constructor(e){this.computed=e,this.version=0,this.activeLink=void 0,this.subs=void 0,this.map=void 0,this.key=void 0,this.sc=0}track(e){if(!J||!ge||J===this.computed)return;let t=this.activeLink;if(void 0===t||t.sub!==J)t=this.activeLink=new _e(J,this),J.deps?(t.prevDep=J.depsTail,J.depsTail.nextDep=t,J.depsTail=t):J.deps=J.depsTail=t,xe(t);else if(-1===t.version&&(t.version=this.version,t.nextDep)){const e=t.nextDep;e.prevDep=t.prevDep,t.prevDep&&(t.prevDep.nextDep=e),t.prevDep=J.depsTail,t.nextDep=void 0,J.depsTail.nextDep=t,J.depsTail=t,J.deps===t&&(J.deps=e)}return t}trigger(e){this.version++,be++,this.notify(e)}notify(e){le();try{0;for(let e=this.subs;e;e=e.prevSub)e.sub.notify()&&e.sub.dep.notify()}finally{ae()}}}function xe(e){if(e.dep.sc++,4&e.sub.flags){const t=e.dep.computed;if(t&&!e.dep.subs){t.flags|=20;for(let e=t.deps;e;e=e.nextDep)xe(e)}const n=e.dep.subs;n!==e&&(e.prevSub=n,n&&(n.nextSub=e)),e.dep.subs=e}}const Se=new WeakMap,ke=Symbol(""),Te=Symbol(""),$e=Symbol("");function Le(e,t,n){if(ge&&J){let t=Se.get(e);t||Se.set(e,t=new Map);let o=t.get(n);o||(t.set(n,o=new Ce),o.map=t,o.key=n),o.track()}}function Pe(e,t,n,o,i,s){const r=Se.get(e);if(!r)return void be++;const l=e=>{e&&e.trigger()};if(le(),"clear"===t)r.forEach(l);else{const i=d(e),s=i&&x(n);if(i&&"length"===n){const e=Number(o);r.forEach(((t,n)=>{("length"===n||n===$e||!m(n)&&n>=e)&&l(t)}))}else switch((void 0!==n||r.has(void 0))&&l(r.get(n)),s&&l(r.get($e)),t){case"add":i?s&&l(r.get("length")):(l(r.get(ke)),p(e)&&l(r.get(Te)));break;case"delete":i||(l(r.get(ke)),p(e)&&l(r.get(Te)));break;case"set":p(e)&&l(r.get(ke))}}ae()}function Oe(e){const t=ft(e);return t===e?t:(Le(t,0,$e),pt(e)?t:t.map(gt))}function Me(e){return Le(e=ft(e),0,$e),e}const Ae={__proto__:null,[Symbol.iterator](){return Ee(this,Symbol.iterator,gt)},concat(...e){return Oe(this).concat(...e.map((e=>d(e)?Oe(e):e)))},entries(){return Ee(this,"entries",(e=>(e[1]=gt(e[1]),e)))},every(e,t){return Re(this,"every",e,t,void 0,arguments)},filter(e,t){return Re(this,"filter",e,t,(e=>e.map(gt)),arguments)},find(e,t){return Re(this,"find",e,t,gt,arguments)},findIndex(e,t){return Re(this,"findIndex",e,t,void 0,arguments)},findLast(e,t){return Re(this,"findLast",e,t,gt,arguments)},findLastIndex(e,t){return Re(this,"findLastIndex",e,t,void 0,arguments)},forEach(e,t){return Re(this,"forEach",e,t,void 0,arguments)},includes(...e){return je(this,"includes",e)},indexOf(...e){return je(this,"indexOf",e)},join(e){return Oe(this).join(e)},lastIndexOf(...e){return je(this,"lastIndexOf",e)},map(e,t){return Re(this,"map",e,t,void 0,arguments)},pop(){return ze(this,"pop")},push(...e){return ze(this,"push",e)},reduce(e,...t){return He(this,"reduce",e,t)},reduceRight(e,...t){return He(this,"reduceRight",e,t)},shift(){return ze(this,"shift")},some(e,t){return Re(this,"some",e,t,void 0,arguments)},splice(...e){return ze(this,"splice",e)},toReversed(){return Oe(this).toReversed()},toSorted(e){return Oe(this).toSorted(e)},toSpliced(...e){return Oe(this).toSpliced(...e)},unshift(...e){return ze(this,"unshift",e)},values(){return Ee(this,"values",gt)}};function Ee(e,t,n){const o=Me(e),i=o[t]();return o===e||pt(e)||(i._next=i.next,i.next=()=>{const e=i._next();return e.value&&(e.value=n(e.value)),e}),i}const De=Array.prototype;function Re(e,t,n,o,i,s){const r=Me(e),l=r!==e&&!pt(e),a=r[t];if(a!==De[t]){const t=a.apply(e,s);return l?gt(t):t}let c=n;r!==e&&(l?c=function(t,o){return n.call(this,gt(t),o,e)}:n.length>2&&(c=function(t,o){return n.call(this,t,o,e)}));const u=a.call(r,c,o);return l&&i?i(u):u}function He(e,t,n,o){const i=Me(e);let s=n;return i!==e&&(pt(e)?n.length>3&&(s=function(t,o,i){return n.call(this,t,o,i,e)}):s=function(t,o,i){return n.call(this,t,gt(o),i,e)}),i[t](s,...o)}function je(e,t,n){const o=ft(e);Le(o,0,$e);const i=o[t](...n);return-1!==i&&!1!==i||!ht(n[0])?i:(n[0]=ft(n[0]),o[t](...n))}function ze(e,t,n=[]){ve(),le();const o=ft(e)[t].apply(e,n);return ae(),ye(),o}const Be=e("__proto__,__v_isRef,__isVue"),Ne=new Set(Object.getOwnPropertyNames(Symbol).filter((e=>"arguments"!==e&&"caller"!==e)).map((e=>Symbol[e])).filter(m));function Fe(e){m(e)||(e=String(e));const t=ft(this);return Le(t,0,e),t.hasOwnProperty(e)}class Ve{constructor(e=!1,t=!1){this._isReadonly=e,this._isShallow=t}get(e,t,n){if("__v_skip"===t)return e.__v_skip;const o=this._isReadonly,i=this._isShallow;if("__v_isReactive"===t)return!o;if("__v_isReadonly"===t)return o;if("__v_isShallow"===t)return i;if("__v_raw"===t)return n===(o?i?st:it:i?ot:nt).get(e)||Object.getPrototypeOf(e)===Object.getPrototypeOf(n)?e:void 0;const s=d(e);if(!o){let e;if(s&&(e=Ae[t]))return e;if("hasOwnProperty"===t)return Fe}const r=Reflect.get(e,t,vt(e)?e:n);return(m(t)?Ne.has(t):Be(t))?r:(o||Le(e,0,t),i?r:vt(r)?s&&x(t)?r:r.value:v(r)?o?at(r):lt(r):r)}}class Ie extends Ve{constructor(e=!1){super(!1,e)}set(e,t,n,o){let i=e[t];if(!this._isShallow){const t=dt(i);if(pt(n)||dt(n)||(i=ft(i),n=ft(n)),!d(e)&&vt(i)&&!vt(n))return!t&&(i.value=n,!0)}const s=d(e)&&x(t)?Number(t)<e.length:u(e,t),r=Reflect.set(e,t,n,vt(e)?e:o);return e===ft(o)&&(s?E(n,i)&&Pe(e,"set",t,n):Pe(e,"add",t,n)),r}deleteProperty(e,t){const n=u(e,t);e[t];const o=Reflect.deleteProperty(e,t);return o&&n&&Pe(e,"delete",t,void 0),o}has(e,t){const n=Reflect.has(e,t);return m(t)&&Ne.has(t)||Le(e,0,t),n}ownKeys(e){return Le(e,0,d(e)?"length":ke),Reflect.ownKeys(e)}}class Ue extends Ve{constructor(e=!1){super(!0,e)}set(e,t){return!0}deleteProperty(e,t){return!0}}const We=new Ie,Ze=new Ue,qe=new Ie(!0),Ge=e=>e,Ye=e=>Reflect.getPrototypeOf(e);function Ke(e){return function(...t){return"delete"!==e&&("clear"===e?void 0:this)}}function Xe(e,t){const n={get(n){const o=this.__v_raw,i=ft(o),s=ft(n);e||(E(n,s)&&Le(i,0,n),Le(i,0,s));const{has:r}=Ye(i),l=t?Ge:e?mt:gt;return r.call(i,n)?l(o.get(n)):r.call(i,s)?l(o.get(s)):void(o!==i&&o.get(n))},get size(){const t=this.__v_raw;return!e&&Le(ft(t),0,ke),Reflect.get(t,"size",t)},has(t){const n=this.__v_raw,o=ft(n),i=ft(t);return e||(E(t,i)&&Le(o,0,t),Le(o,0,i)),t===i?n.has(t):n.has(t)||n.has(i)},forEach(n,o){const i=this,s=i.__v_raw,r=ft(s),l=t?Ge:e?mt:gt;return!e&&Le(r,0,ke),s.forEach(((e,t)=>n.call(o,l(e),l(t),i)))}};l(n,e?{add:Ke("add"),set:Ke("set"),delete:Ke("delete"),clear:Ke("clear")}:{add(e){t||pt(e)||dt(e)||(e=ft(e));const n=ft(this);return Ye(n).has.call(n,e)||(n.add(e),Pe(n,"add",e,e)),this},set(e,n){t||pt(n)||dt(n)||(n=ft(n));const o=ft(this),{has:i,get:s}=Ye(o);let r=i.call(o,e);r||(e=ft(e),r=i.call(o,e));const l=s.call(o,e);return o.set(e,n),r?E(n,l)&&Pe(o,"set",e,n):Pe(o,"add",e,n),this},delete(e){const t=ft(this),{has:n,get:o}=Ye(t);let i=n.call(t,e);i||(e=ft(e),i=n.call(t,e)),o&&o.call(t,e);const s=t.delete(e);return i&&Pe(t,"delete",e,void 0),s},clear(){const e=ft(this),t=0!==e.size,n=e.clear();return t&&Pe(e,"clear",void 0,void 0),n}});return["keys","values","entries",Symbol.iterator].forEach((o=>{n[o]=function(e,t,n){return function(...o){const i=this.__v_raw,s=ft(i),r=p(s),l="entries"===e||e===Symbol.iterator&&r,a="keys"===e&&r,c=i[e](...o),u=n?Ge:t?mt:gt;return!t&&Le(s,0,a?Te:ke),{next(){const{value:e,done:t}=c.next();return t?{value:e,done:t}:{value:l?[u(e[0]),u(e[1])]:u(e),done:t}},[Symbol.iterator](){return this}}}}(o,e,t)})),n}function Qe(e,t){const n=Xe(e,t);return(t,o,i)=>"__v_isReactive"===o?!e:"__v_isReadonly"===o?e:"__v_raw"===o?t:Reflect.get(u(n,o)&&o in t?n:t,o,i)}const Je={get:Qe(!1,!1)},et={get:Qe(!1,!0)},tt={get:Qe(!0,!1)},nt=new WeakMap,ot=new WeakMap,it=new WeakMap,st=new WeakMap;function rt(e){return e.__v_skip||!Object.isExtensible(e)?0:function(e){switch(e){case"Object":case"Array":return 1;case"Map":case"Set":case"WeakMap":case"WeakSet":return 2;default:return 0}}((e=>b(e).slice(8,-1))(e))}function lt(e){return dt(e)?e:ct(e,!1,We,Je,nt)}function at(e){return ct(e,!0,Ze,tt,it)}function ct(e,t,n,o,i){if(!v(e))return e;if(e.__v_raw&&(!t||!e.__v_isReactive))return e;const s=rt(e);if(0===s)return e;const r=i.get(e);if(r)return r;const l=new Proxy(e,2===s?o:n);return i.set(e,l),l}function ut(e){return dt(e)?ut(e.__v_raw):!(!e||!e.__v_isReactive)}function dt(e){return!(!e||!e.__v_isReadonly)}function pt(e){return!(!e||!e.__v_isShallow)}function ht(e){return!!e&&!!e.__v_raw}function ft(e){const t=e&&e.__v_raw;return t?ft(t):e}const gt=e=>v(e)?lt(e):e,mt=e=>v(e)?at(e):e;function vt(e){return!!e&&!0===e.__v_isRef}function yt(e){return function(e,t){if(vt(e))return e;return new wt(e,t)}(e,!1)}class wt{constructor(e,t){this.dep=new Ce,this.__v_isRef=!0,this.__v_isShallow=!1,this._rawValue=t?e:ft(e),this._value=t?e:gt(e),this.__v_isShallow=t}get value(){return this.dep.track(),this._value}set value(e){const t=this._rawValue,n=this.__v_isShallow||pt(e)||dt(e);e=n?e:ft(e),E(e,t)&&(this._rawValue=e,this._value=n?e:gt(e),this.dep.trigger())}}const bt={get:(e,t,n)=>{return"__v_raw"===t?e:vt(o=Reflect.get(e,t,n))?o.value:o;var o},set:(e,t,n,o)=>{const i=e[t];return vt(i)&&!vt(n)?(i.value=n,!0):Reflect.set(e,t,n,o)}};function _t(e){return ut(e)?e:new Proxy(e,bt)}class Ct{constructor(e,t,n){this.fn=e,this.setter=t,this._value=void 0,this.dep=new Ce(this),this.__v_isRef=!0,this.deps=void 0,this.depsTail=void 0,this.flags=16,this.globalVersion=be-1,this.next=void 0,this.effect=this,this.__v_isReadonly=!t,this.isSSR=n}notify(){if(this.flags|=16,!(8&this.flags)&&J!==this)return re(this,!0),!0}get value(){const e=this.dep.track();return pe(this),e&&(e.version=this.dep.version),this._value}set value(e){this.setter&&this.setter(e)}}const xt={},St=new WeakMap;let kt;function Tt(e,n,i=t){const{immediate:s,deep:r,once:l,scheduler:c,augmentJob:u,call:p}=i,h=e=>r?e:pt(e)||!1===r||0===r?$t(e,1):$t(e);let g,m,v,y,w=!1,b=!1;if(vt(e)?(m=()=>e.value,w=pt(e)):ut(e)?(m=()=>h(e),w=!0):d(e)?(b=!0,w=e.some((e=>ut(e)||pt(e))),m=()=>e.map((e=>vt(e)?e.value:ut(e)?h(e):f(e)?p?p(e,2):e():void 0))):m=f(e)?n?p?()=>p(e,2):e:()=>{if(v){ve();try{v()}finally{ye()}}const t=kt;kt=g;try{return p?p(e,3,[y]):e(y)}finally{kt=t}}:o,n&&r){const e=m,t=!0===r?1/0:r;m=()=>$t(e(),t)}const C=Q,x=()=>{g.stop(),C&&C.active&&a(C.effects,g)};if(l&&n){const e=n;n=(...t)=>{e(...t),x()}}let S=b?new Array(e.length).fill(xt):xt;const k=e=>{if(1&g.flags&&(g.dirty||e))if(n){const e=g.run();if(r||w||(b?e.some(((e,t)=>E(e,S[t]))):E(e,S))){v&&v();const t=kt;kt=g;try{const t=[e,S===xt?void 0:b&&S[0]===xt?[]:S,y];p?p(n,3,t):n(...t),S=e}finally{kt=t}}}else g.run()};return u&&u(k),g=new ne(m),g.scheduler=c?()=>c(k,!1):k,y=e=>function(e,t=!1,n=kt){if(n){let t=St.get(n);t||St.set(n,t=[]),t.push(e)}}(e,!1,g),v=g.onStop=()=>{const e=St.get(g);if(e){if(p)p(e,4);else for(const t of e)t();St.delete(g)}},n?s?k(!0):S=g.run():c?c(k.bind(null,!0),!0):g.run(),x.pause=g.pause.bind(g),x.resume=g.resume.bind(g),x.stop=x,x}function $t(e,t=1/0,n){if(t<=0||!v(e)||e.__v_skip)return e;if((n=n||new Set).has(e))return e;if(n.add(e),t--,vt(e))$t(e.value,t,n);else if(d(e))for(let o=0;o<e.length;o++)$t(e[o],t,n);else if(h(e)||p(e))e.forEach((e=>{$t(e,t,n)}));else if(C(e)){for(const o in e)$t(e[o],t,n);for(const o of Object.getOwnPropertySymbols(e))Object.prototype.propertyIsEnumerable.call(e,o)&&$t(e[o],t,n)}return e}function Lt(e,t,n,o){try{return o?e(...o):e()}catch(i){Ot(i,t,n)}}function Pt(e,t,n,o){if(f(e)){const i=Lt(e,t,n,o);return i&&y(i)&&i.catch((e=>{Ot(e,t,n)})),i}if(d(e)){const i=[];for(let s=0;s<e.length;s++)i.push(Pt(e[s],t,n,o));return i}}function Ot(e,n,o,i=!0){n&&n.vnode;const{errorHandler:s,throwUnhandledErrorInProduction:r}=n&&n.appContext.config||t;if(n){let t=n.parent;const i=n.proxy,r=`https://vuejs.org/error-reference/#runtime-${o}`;for(;t;){const n=t.ec;if(n)for(let t=0;t<n.length;t++)if(!1===n[t](e,i,r))return;t=t.parent}if(s)return ve(),Lt(s,null,10,[e,i,r]),void ye()}!function(e,t,n,o=!0,i=!1){if(i)throw e;console.error(e)}(e,0,0,i,r)}const Mt=[];let At=-1;const Et=[];let Dt=null,Rt=0;const Ht=Promise.resolve();let jt=null;function zt(e){const t=jt||Ht;return e?t.then(this?e.bind(this):e):t}function Bt(e){if(!(1&e.flags)){const t=It(e),n=Mt[Mt.length-1];!n||!(2&e.flags)&&t>=It(n)?Mt.push(e):Mt.splice(function(e){let t=At+1,n=Mt.length;for(;t<n;){const o=t+n>>>1,i=Mt[o],s=It(i);s<e||s===e&&2&i.flags?t=o+1:n=o}return t}(t),0,e),e.flags|=1,Nt()}}function Nt(){jt||(jt=Ht.then(Ut))}function Ft(e,t,n=At+1){for(;n<Mt.length;n++){const t=Mt[n];if(t&&2&t.flags){if(e&&t.id!==e.uid)continue;Mt.splice(n,1),n--,4&t.flags&&(t.flags&=-2),t(),4&t.flags||(t.flags&=-2)}}}function Vt(e){if(Et.length){const e=[...new Set(Et)].sort(((e,t)=>It(e)-It(t)));if(Et.length=0,Dt)return void Dt.push(...e);for(Dt=e,Rt=0;Rt<Dt.length;Rt++){const e=Dt[Rt];4&e.flags&&(e.flags&=-2),8&e.flags||e(),e.flags&=-2}Dt=null,Rt=0}}const It=e=>null==e.id?2&e.flags?-1:1/0:e.id;function Ut(e){try{for(At=0;At<Mt.length;At++){const e=Mt[At];!e||8&e.flags||(4&e.flags&&(e.flags&=-2),Lt(e,e.i,e.i?15:14),4&e.flags||(e.flags&=-2))}}finally{for(;At<Mt.length;At++){const e=Mt[At];e&&(e.flags&=-2)}At=-1,Mt.length=0,Vt(),jt=null,(Mt.length||Et.length)&&Ut()}}let Wt=null,Zt=null;function qt(e){const t=Wt;return Wt=e,Zt=e&&e.type.__scopeId||null,t}const Gt=e=>Yt;function Yt(e,t=Wt,n){if(!t)return e;if(e._n)return e;const o=(...n)=>{o._d&&Wo(-1);const i=qt(t);let s;try{s=e(...n)}finally{qt(i),o._d&&Wo(1)}return s};return o._n=!0,o._c=!0,o._d=!0,o}function Kt(e,n){if(null===Wt)return e;const o=xi(Wt),i=e.dirs||(e.dirs=[]);for(let s=0;s<n.length;s++){let[e,r,l,a=t]=n[s];e&&(f(e)&&(e={mounted:e,updated:e}),e.deep&&$t(r),i.push({dir:e,instance:o,value:r,oldValue:void 0,arg:l,modifiers:a}))}return e}function Xt(e,t,n,o){const i=e.dirs,s=t&&t.dirs;for(let r=0;r<i.length;r++){const l=i[r];s&&(l.oldValue=s[r].value);let a=l.dir[o];a&&(ve(),Pt(a,n,8,[e.el,l,e,t]),ye())}}const Qt=Symbol("_vte");function Jt(e,t){6&e.shapeFlag&&e.component?(e.transition=t,Jt(e.component.subTree,t)):128&e.shapeFlag?(e.ssContent.transition=t.clone(e.ssContent),e.ssFallback.transition=t.clone(e.ssFallback)):e.transition=t}function en(e,t){return f(e)?(()=>l({name:e.name},t,{setup:e}))():e}function tn(e){e.ids=[e.ids[0]+e.ids[2]+++"-",0,0]}function nn(e,n,o,i,s=!1){if(d(e))return void e.forEach(((e,t)=>nn(e,n&&(d(n)?n[t]:n),o,i,s)));if(on(i)&&!s)return void(512&i.shapeFlag&&i.type.__asyncResolved&&i.component.subTree.component&&nn(e,n,o,i.component.subTree));const r=4&i.shapeFlag?xi(i.component):i.el,l=s?null:r,{i:c,r:p}=e,h=n&&n.r,m=c.refs===t?c.refs={}:c.refs,v=c.setupState,y=ft(v),w=v===t?()=>!1:e=>u(y,e);if(null!=h&&h!==p&&(g(h)?(m[h]=null,w(h)&&(v[h]=null)):vt(h)&&(h.value=null)),f(p))Lt(p,c,12,[l,m]);else{const t=g(p),n=vt(p);if(t||n){const i=()=>{if(e.f){const n=t?w(p)?v[p]:m[p]:p.value;s?d(n)&&a(n,r):d(n)?n.includes(r)||n.push(r):t?(m[p]=[r],w(p)&&(v[p]=m[p])):(p.value=[r],e.k&&(m[e.k]=p.value))}else t?(m[p]=l,w(p)&&(v[p]=l)):n&&(p.value=l,e.k&&(m[e.k]=l))};l?(i.id=-1,go(i,o)):i()}}}z().requestIdleCallback,z().cancelIdleCallback;const on=e=>!!e.type.__asyncLoader,sn=e=>e.type.__isKeepAlive;function rn(e,t){an(e,"a",t)}function ln(e,t){an(e,"da",t)}function an(e,t,n=gi){const o=e.__wdc||(e.__wdc=()=>{let t=n;for(;t;){if(t.isDeactivated)return;t=t.parent}return e()});if(un(t,o,n),n){let e=n.parent;for(;e&&e.parent;)sn(e.parent.vnode)&&cn(o,t,n,e),e=e.parent}}function cn(e,t,n,o){const i=un(t,e,o,!0);vn((()=>{a(o[t],i)}),n)}function un(e,t,n=gi,o=!1){if(n){const i=n[e]||(n[e]=[]),s=t.__weh||(t.__weh=(...o)=>{ve();const i=mi(n),s=Pt(t,n,e,o);return i(),ye(),s});return o?i.unshift(s):i.push(s),s}}const dn=e=>(t,n=gi)=>{wi&&"sp"!==e||un(e,((...e)=>t(...e)),n)},pn=dn("bm"),hn=dn("m"),fn=dn("bu"),gn=dn("u"),mn=dn("bum"),vn=dn("um"),yn=dn("sp"),wn=dn("rtg"),bn=dn("rtc");function _n(e,t=gi){un("ec",e,t)}const Cn="components";function xn(e,t){return Tn(Cn,e,!0,t)||e}const Sn=Symbol.for("v-ndc");function kn(e){return Tn("directives",e)}function Tn(e,t,n=!0,o=!1){const i=Wt||gi;if(i){const n=i.type;if(e===Cn){const e=Si(n,!1);if(e&&(e===t||e===L(t)||e===M(L(t))))return n}const s=$n(i[e]||n[e],t)||$n(i.appContext[e],t);return!s&&o?n:s}}function $n(e,t){return e&&(e[t]||e[L(t)]||e[M(L(t))])}function Ln(e,t,n,o){let i;const s=n,r=d(e);if(r||g(e)){let n=!1,o=!1;r&&ut(e)&&(n=!pt(e),o=dt(e),e=Me(e)),i=new Array(e.length);for(let r=0,l=e.length;r<l;r++)i[r]=t(n?o?mt(gt(e[r])):gt(e[r]):e[r],r,void 0,s)}else if("number"==typeof e){i=new Array(e);for(let n=0;n<e;n++)i[n]=t(n+1,n,void 0,s)}else if(v(e))if(e[Symbol.iterator])i=Array.from(e,((e,n)=>t(e,n,void 0,s)));else{const n=Object.keys(e);i=new Array(n.length);for(let o=0,r=n.length;o<r;o++){const r=n[o];i[o]=t(e[r],r,o,s)}}else i=[];return i}function Pn(e,t,n={},o,i){if(Wt.ce||Wt.parent&&on(Wt.parent)&&Wt.parent.ce)return"default"!==t&&(n.name=t),Io(),Go(jo,null,[ei("slot",n,o)],64);let s=e[t];s&&s._c&&(s._d=!1),Io();const r=s&&On(s(n)),l=n.key||r&&r.key,a=Go(jo,{key:(l&&!m(l)?l:`_${t}`)+""},r||[],r&&1===e._?64:-2);return a.scopeId&&(a.slotScopeIds=[a.scopeId+"-s"]),s&&s._c&&(s._d=!0),a}function On(e){return e.some((e=>!Yo(e)||e.type!==Bo&&!(e.type===jo&&!On(e.children))))?e:null}const Mn=e=>e?yi(e)?xi(e):Mn(e.parent):null,An=l(Object.create(null),{$:e=>e,$el:e=>e.vnode.el,$data:e=>e.data,$props:e=>e.props,$attrs:e=>e.attrs,$slots:e=>e.slots,$refs:e=>e.refs,$parent:e=>Mn(e.parent),$root:e=>Mn(e.root),$host:e=>e.ce,$emit:e=>e.emit,$options:e=>Nn(e),$forceUpdate:e=>e.f||(e.f=()=>{Bt(e.update)}),$nextTick:e=>e.n||(e.n=zt.bind(e.proxy)),$watch:e=>To.bind(e)}),En=(e,n)=>e!==t&&!e.__isScriptSetup&&u(e,n),Dn={get({_:e},n){if("__v_skip"===n)return!0;const{ctx:o,setupState:i,data:s,props:r,accessCache:l,type:a,appContext:c}=e;let d;if("$"!==n[0]){const a=l[n];if(void 0!==a)switch(a){case 1:return i[n];case 2:return s[n];case 4:return o[n];case 3:return r[n]}else{if(En(i,n))return l[n]=1,i[n];if(s!==t&&u(s,n))return l[n]=2,s[n];if((d=e.propsOptions[0])&&u(d,n))return l[n]=3,r[n];if(o!==t&&u(o,n))return l[n]=4,o[n];Hn&&(l[n]=0)}}const p=An[n];let h,f;return p?("$attrs"===n&&Le(e.attrs,0,""),p(e)):(h=a.__cssModules)&&(h=h[n])?h:o!==t&&u(o,n)?(l[n]=4,o[n]):(f=c.config.globalProperties,u(f,n)?f[n]:void 0)},set({_:e},n,o){const{data:i,setupState:s,ctx:r}=e;return En(s,n)?(s[n]=o,!0):i!==t&&u(i,n)?(i[n]=o,!0):!u(e.props,n)&&(("$"!==n[0]||!(n.slice(1)in e))&&(r[n]=o,!0))},has({_:{data:e,setupState:n,accessCache:o,ctx:i,appContext:s,propsOptions:r}},l){let a;return!!o[l]||e!==t&&u(e,l)||En(n,l)||(a=r[0])&&u(a,l)||u(i,l)||u(An,l)||u(s.config.globalProperties,l)},defineProperty(e,t,n){return null!=n.get?e._.accessCache[t]=0:u(n,"value")&&this.set(e,t,n.value,null),Reflect.defineProperty(e,t,n)}};function Rn(e){return d(e)?e.reduce(((e,t)=>(e[t]=null,e)),{}):e}let Hn=!0;function jn(e){const t=Nn(e),n=e.proxy,i=e.ctx;Hn=!1,t.beforeCreate&&zn(t.beforeCreate,e,"bc");const{data:s,computed:r,methods:l,watch:a,provide:c,inject:u,created:p,beforeMount:h,mounted:g,beforeUpdate:m,updated:y,activated:w,deactivated:b,beforeDestroy:C,beforeUnmount:x,destroyed:S,unmounted:k,render:T,renderTracked:L,renderTriggered:P,errorCaptured:O,serverPrefetch:M,expose:A,inheritAttrs:E,components:D,directives:R,filters:H}=t;if(u&&function(e,t){d(e)&&(e=Un(e));for(const n in e){const o=e[n];let i;i=v(o)?"default"in o?Qn(o.from||n,o.default,!0):Qn(o.from||n):Qn(o),vt(i)?Object.defineProperty(t,n,{enumerable:!0,configurable:!0,get:()=>i.value,set:e=>i.value=e}):t[n]=i}}(u,i,null),l)for(const o in l){const e=l[o];f(e)&&(i[o]=e.bind(n))}if(s){const t=s.call(n,n);v(t)&&(e.data=lt(t))}if(Hn=!0,r)for(const d in r){const e=r[d],t=f(e)?e.bind(n,n):f(e.get)?e.get.bind(n,n):o,s=!f(e)&&f(e.set)?e.set.bind(n):o,l=ki({get:t,set:s});Object.defineProperty(i,d,{enumerable:!0,configurable:!0,get:()=>l.value,set:e=>l.value=e})}if(a)for(const o in a)Bn(a[o],i,n,o);if(c){const e=f(c)?c.call(n):c;Reflect.ownKeys(e).forEach((t=>{!function(e,t){if(gi){let n=gi.provides;const o=gi.parent&&gi.parent.provides;o===n&&(n=gi.provides=Object.create(o)),n[e]=t}else;}(t,e[t])}))}function j(e,t){d(t)?t.forEach((t=>e(t.bind(n)))):t&&e(t.bind(n))}if(p&&zn(p,e,"c"),j(pn,h),j(hn,g),j(fn,m),j(gn,y),j(rn,w),j(ln,b),j(_n,O),j(bn,L),j(wn,P),j(mn,x),j(vn,k),j(yn,M),d(A))if(A.length){const t=e.exposed||(e.exposed={});A.forEach((e=>{Object.defineProperty(t,e,{get:()=>n[e],set:t=>n[e]=t})}))}else e.exposed||(e.exposed={});T&&e.render===o&&(e.render=T),null!=E&&(e.inheritAttrs=E),D&&(e.components=D),R&&(e.directives=R),M&&tn(e)}function zn(e,t,n){Pt(d(e)?e.map((e=>e.bind(t.proxy))):e.bind(t.proxy),t,n)}function Bn(e,t,n,o){let i=o.includes(".")?$o(n,o):()=>n[o];if(g(e)){const n=t[e];f(n)&&So(i,n)}else if(f(e))So(i,e.bind(n));else if(v(e))if(d(e))e.forEach((e=>Bn(e,t,n,o)));else{const o=f(e.handler)?e.handler.bind(n):t[e.handler];f(o)&&So(i,o,e)}}function Nn(e){const t=e.type,{mixins:n,extends:o}=t,{mixins:i,optionsCache:s,config:{optionMergeStrategies:r}}=e.appContext,l=s.get(t);let a;return l?a=l:i.length||n||o?(a={},i.length&&i.forEach((e=>Fn(a,e,r,!0))),Fn(a,t,r)):a=t,v(t)&&s.set(t,a),a}function Fn(e,t,n,o=!1){const{mixins:i,extends:s}=t;s&&Fn(e,s,n,!0),i&&i.forEach((t=>Fn(e,t,n,!0)));for(const r in t)if(o&&"expose"===r);else{const o=Vn[r]||n&&n[r];e[r]=o?o(e[r],t[r]):t[r]}return e}const Vn={data:In,props:qn,emits:qn,methods:Zn,computed:Zn,beforeCreate:Wn,created:Wn,beforeMount:Wn,mounted:Wn,beforeUpdate:Wn,updated:Wn,beforeDestroy:Wn,beforeUnmount:Wn,destroyed:Wn,unmounted:Wn,activated:Wn,deactivated:Wn,errorCaptured:Wn,serverPrefetch:Wn,components:Zn,directives:Zn,watch:function(e,t){if(!e)return t;if(!t)return e;const n=l(Object.create(null),e);for(const o in t)n[o]=Wn(e[o],t[o]);return n},provide:In,inject:function(e,t){return Zn(Un(e),Un(t))}};function In(e,t){return t?e?function(){return l(f(e)?e.call(this,this):e,f(t)?t.call(this,this):t)}:t:e}function Un(e){if(d(e)){const t={};for(let n=0;n<e.length;n++)t[e[n]]=e[n];return t}return e}function Wn(e,t){return e?[...new Set([].concat(e,t))]:t}function Zn(e,t){return e?l(Object.create(null),e,t):t}function qn(e,t){return e?d(e)&&d(t)?[...new Set([...e,...t])]:l(Object.create(null),Rn(e),Rn(null!=t?t:{})):t}function Gn(){return{app:null,config:{isNativeTag:i,performance:!1,globalProperties:{},optionMergeStrategies:{},errorHandler:void 0,warnHandler:void 0,compilerOptions:{}},mixins:[],components:{},directives:{},provides:Object.create(null),optionsCache:new WeakMap,propsCache:new WeakMap,emitsCache:new WeakMap}}let Yn=0;function Kn(e,t){return function(t,n=null){f(t)||(t=l({},t)),null==n||v(n)||(n=null);const o=Gn(),i=new WeakSet,s=[];let r=!1;const a=o.app={_uid:Yn++,_component:t,_props:n,_container:null,_context:o,_instance:null,version:Ti,get config(){return o.config},set config(e){},use:(e,...t)=>(i.has(e)||(e&&f(e.install)?(i.add(e),e.install(a,...t)):f(e)&&(i.add(e),e(a,...t))),a),mixin:e=>(o.mixins.includes(e)||o.mixins.push(e),a),component:(e,t)=>t?(o.components[e]=t,a):o.components[e],directive:(e,t)=>t?(o.directives[e]=t,a):o.directives[e],mount(i,s,l){if(!r){const s=a._ceVNode||ei(t,n);return s.appContext=o,!0===l?l="svg":!1===l&&(l=void 0),e(s,i,l),r=!0,a._container=i,i.__vue_app__=a,xi(s.component)}},onUnmount(e){s.push(e)},unmount(){r&&(Pt(s,a._instance,16),e(null,a._container),delete a._container.__vue_app__)},provide:(e,t)=>(o.provides[e]=t,a),runWithContext(e){const t=Xn;Xn=a;try{return e()}finally{Xn=t}}};return a}}let Xn=null;function Qn(e,t,n=!1){const o=gi||Wt;if(o||Xn){const i=Xn?Xn._context.provides:o?null==o.parent?o.vnode.appContext&&o.vnode.appContext.provides:o.parent.provides:void 0;if(i&&e in i)return i[e];if(arguments.length>1)return n&&f(t)?t.call(o&&o.proxy):t}}const Jn={},eo=()=>Object.create(Jn),to=e=>Object.getPrototypeOf(e)===Jn;function no(e,t,n,o=!1){const i={},s=eo();e.propsDefaults=Object.create(null),oo(e,t,i,s);for(const r in e.propsOptions[0])r in i||(i[r]=void 0);n?e.props=o?i:ct(i,!1,qe,et,ot):e.type.props?e.props=i:e.props=s,e.attrs=s}function oo(e,n,o,i){const[s,r]=e.propsOptions;let l,a=!1;if(n)for(let t in n){if(S(t))continue;const c=n[t];let d;s&&u(s,d=L(t))?r&&r.includes(d)?(l||(l={}))[d]=c:o[d]=c:Mo(e.emitsOptions,t)||t in i&&c===i[t]||(i[t]=c,a=!0)}if(r){const n=ft(o),i=l||t;for(let t=0;t<r.length;t++){const l=r[t];o[l]=io(s,n,l,i[l],e,!u(i,l))}}return a}function io(e,t,n,o,i,s){const r=e[n];if(null!=r){const e=u(r,"default");if(e&&void 0===o){const e=r.default;if(r.type!==Function&&!r.skipFactory&&f(e)){const{propsDefaults:s}=i;if(n in s)o=s[n];else{const r=mi(i);o=s[n]=e.call(null,t),r()}}else o=e;i.ce&&i.ce._setProp(n,o)}r[0]&&(s&&!e?o=!1:!r[1]||""!==o&&o!==O(n)||(o=!0))}return o}const so=new WeakMap;function ro(e,o,i=!1){const s=i?so:o.propsCache,r=s.get(e);if(r)return r;const a=e.props,c={},p=[];let h=!1;if(!f(e)){const t=e=>{h=!0;const[t,n]=ro(e,o,!0);l(c,t),n&&p.push(...n)};!i&&o.mixins.length&&o.mixins.forEach(t),e.extends&&t(e.extends),e.mixins&&e.mixins.forEach(t)}if(!a&&!h)return v(e)&&s.set(e,n),n;if(d(a))for(let n=0;n<a.length;n++){const e=L(a[n]);lo(e)&&(c[e]=t)}else if(a)for(const t in a){const e=L(t);if(lo(e)){const n=a[t],o=c[e]=d(n)||f(n)?{type:n}:l({},n),i=o.type;let s=!1,r=!0;if(d(i))for(let e=0;e<i.length;++e){const t=i[e],n=f(t)&&t.name;if("Boolean"===n){s=!0;break}"String"===n&&(r=!1)}else s=f(i)&&"Boolean"===i.name;o[0]=s,o[1]=r,(s||u(o,"default"))&&p.push(e)}}const g=[c,p];return v(e)&&s.set(e,g),g}function lo(e){return"$"!==e[0]&&!S(e)}const ao=e=>"_"===e[0]||"$stable"===e,co=e=>d(e)?e.map(ri):[ri(e)],uo=(e,t,n)=>{if(t._n)return t;const o=Yt(((...e)=>co(t(...e))),n);return o._c=!1,o},po=(e,t,n)=>{const o=e._ctx;for(const i in e){if(ao(i))continue;const n=e[i];if(f(n))t[i]=uo(0,n,o);else if(null!=n){const e=co(n);t[i]=()=>e}}},ho=(e,t)=>{const n=co(t);e.slots.default=()=>n},fo=(e,t,n)=>{for(const o in t)!n&&ao(o)||(e[o]=t[o])},go=function(e,t){t&&t.pendingBranch?d(e)?t.effects.push(...e):t.effects.push(e):(d(n=e)?Et.push(...n):Dt&&-1===n.id?Dt.splice(Rt+1,0,n):1&n.flags||(Et.push(n),n.flags|=1),Nt());var n};function mo(e){return function(e){z().__VUE__=!0;const{insert:i,remove:s,patchProp:r,createElement:l,createText:a,createComment:c,setText:p,setElementText:h,parentNode:f,nextSibling:g,setScopeId:m=o,insertStaticContent:v}=e,w=(e,t,n,o=null,i=null,s=null,r=void 0,l=null,a=!!t.dynamicChildren)=>{if(e===t)return;e&&!Ko(e,t)&&(o=oe(e),K(e,i,s,!0),e=null),-2===t.patchFlag&&(a=!1,t.dynamicChildren=null);const{type:c,ref:u,shapeFlag:d}=t;switch(c){case zo:b(e,t,n,o);break;case Bo:C(e,t,n,o);break;case No:null==e&&x(t,n,o,r);break;case jo:N(e,t,n,o,i,s,r,l,a);break;default:1&d?P(e,t,n,o,i,s,r,l,a):6&d?F(e,t,n,o,i,s,r,l,a):(64&d||128&d)&&c.process(e,t,n,o,i,s,r,l,a,re)}null!=u&&i&&nn(u,e&&e.ref,s,t||e,!t)},b=(e,t,n,o)=>{if(null==e)i(t.el=a(t.children),n,o);else{const n=t.el=e.el;t.children!==e.children&&p(n,t.children)}},C=(e,t,n,o)=>{null==e?i(t.el=c(t.children||""),n,o):t.el=e.el},x=(e,t,n,o)=>{[e.el,e.anchor]=v(e.children,t,n,o,e.el,e.anchor)},k=({el:e,anchor:t},n,o)=>{let s;for(;e&&e!==t;)s=g(e),i(e,n,o),e=s;i(t,n,o)},T=({el:e,anchor:t})=>{let n;for(;e&&e!==t;)n=g(e),s(e),e=n;s(t)},P=(e,t,n,o,i,s,r,l,a)=>{"svg"===t.type?r="svg":"math"===t.type&&(r="mathml"),null==e?M(t,n,o,i,s,r,l,a):H(e,t,i,s,r,l,a)},M=(e,t,n,o,s,a,c,u)=>{let d,p;const{props:f,shapeFlag:g,transition:m,dirs:v}=e;if(d=e.el=l(e.type,a,f&&f.is,f),8&g?h(d,e.children):16&g&&E(e.children,d,null,o,s,vo(e,a),c,u),v&&Xt(e,null,o,"created"),A(d,e,e.scopeId,c,o),f){for(const e in f)"value"===e||S(e)||r(d,e,null,f[e],a,o);"value"in f&&r(d,"value",null,f.value,a),(p=f.onVnodeBeforeMount)&&ui(p,o,e)}v&&Xt(e,null,o,"beforeMount");const y=function(e,t){return(!e||e&&!e.pendingBranch)&&t&&!t.persisted}(s,m);y&&m.beforeEnter(d),i(d,t,n),((p=f&&f.onVnodeMounted)||y||v)&&go((()=>{p&&ui(p,o,e),y&&m.enter(d),v&&Xt(e,null,o,"mounted")}),s)},A=(e,t,n,o,i)=>{if(n&&m(e,n),o)for(let s=0;s<o.length;s++)m(e,o[s]);if(i){let n=i.subTree;if(t===n||Ho(n.type)&&(n.ssContent===t||n.ssFallback===t)){const t=i.vnode;A(e,t,t.scopeId,t.slotScopeIds,i.parent)}}},E=(e,t,n,o,i,s,r,l,a=0)=>{for(let c=a;c<e.length;c++){const a=e[c]=l?li(e[c]):ri(e[c]);w(null,a,t,n,o,i,s,r,l)}},H=(e,n,o,i,s,l,a)=>{const c=n.el=e.el;let{patchFlag:u,dynamicChildren:d,dirs:p}=n;u|=16&e.patchFlag;const f=e.props||t,g=n.props||t;let m;if(o&&yo(o,!1),(m=g.onVnodeBeforeUpdate)&&ui(m,o,n,e),p&&Xt(n,e,o,"beforeUpdate"),o&&yo(o,!0),(f.innerHTML&&null==g.innerHTML||f.textContent&&null==g.textContent)&&h(c,""),d?j(e.dynamicChildren,d,c,o,i,vo(n,s),l):a||Z(e,n,c,null,o,i,vo(n,s),l,!1),u>0){if(16&u)B(c,f,g,o,s);else if(2&u&&f.class!==g.class&&r(c,"class",null,g.class,s),4&u&&r(c,"style",f.style,g.style,s),8&u){const e=n.dynamicProps;for(let t=0;t<e.length;t++){const n=e[t],i=f[n],l=g[n];l===i&&"value"!==n||r(c,n,i,l,s,o)}}1&u&&e.children!==n.children&&h(c,n.children)}else a||null!=d||B(c,f,g,o,s);((m=g.onVnodeUpdated)||p)&&go((()=>{m&&ui(m,o,n,e),p&&Xt(n,e,o,"updated")}),i)},j=(e,t,n,o,i,s,r)=>{for(let l=0;l<t.length;l++){const a=e[l],c=t[l],u=a.el&&(a.type===jo||!Ko(a,c)||70&a.shapeFlag)?f(a.el):n;w(a,c,u,null,o,i,s,r,!0)}},B=(e,n,o,i,s)=>{if(n!==o){if(n!==t)for(const t in n)S(t)||t in o||r(e,t,n[t],null,s,i);for(const t in o){if(S(t))continue;const l=o[t],a=n[t];l!==a&&"value"!==t&&r(e,t,a,l,s,i)}"value"in o&&r(e,"value",n.value,o.value,s)}},N=(e,t,n,o,s,r,l,c,u)=>{const d=t.el=e?e.el:a(""),p=t.anchor=e?e.anchor:a("");let{patchFlag:h,dynamicChildren:f,slotScopeIds:g}=t;g&&(c=c?c.concat(g):g),null==e?(i(d,n,o),i(p,n,o),E(t.children||[],n,p,s,r,l,c,u)):h>0&&64&h&&f&&e.dynamicChildren?(j(e.dynamicChildren,f,n,s,r,l,c),(null!=t.key||s&&t===s.subTree)&&wo(e,t,!0)):Z(e,t,n,p,s,r,l,c,u)},F=(e,t,n,o,i,s,r,l,a)=>{t.slotScopeIds=l,null==e?512&t.shapeFlag?i.ctx.activate(t,n,o,r,a):V(t,n,o,i,s,r,a):I(e,t,a)},V=(e,n,o,i,s,r,l)=>{const a=e.component=function(e,n,o){const i=e.type,s=(n?n.appContext:e.appContext)||di,r={uid:pi++,vnode:e,type:i,parent:n,appContext:s,root:null,next:null,subTree:null,effect:null,update:null,job:null,scope:new ee(!0),render:null,proxy:null,exposed:null,exposeProxy:null,withProxy:null,provides:n?n.provides:Object.create(s.provides),ids:n?n.ids:["",0,0],accessCache:null,renderCache:[],components:null,directives:null,propsOptions:ro(i,s),emitsOptions:Oo(i,s),emit:null,emitted:null,propsDefaults:t,inheritAttrs:i.inheritAttrs,ctx:t,data:t,props:t,attrs:t,slots:t,refs:t,setupState:t,setupContext:null,suspense:o,suspenseId:o?o.pendingId:0,asyncDep:null,asyncResolved:!1,isMounted:!1,isUnmounted:!1,isDeactivated:!1,bc:null,c:null,bm:null,m:null,bu:null,u:null,um:null,bum:null,da:null,a:null,rtg:null,rtc:null,ec:null,sp:null};r.ctx={_:r},r.root=n?n.root:r,r.emit=Po.bind(null,r),e.ce&&e.ce(r);return r}(e,i,s);if(sn(e)&&(a.ctx.renderer=re),function(e,t=!1,n=!1){t&&fi(t);const{props:o,children:i}=e.vnode,s=yi(e);no(e,o,s,t),((e,t,n)=>{const o=e.slots=eo();if(32&e.vnode.shapeFlag){const e=t._;e?(fo(o,t,n),n&&R(o,"_",e,!0)):po(t,o)}else t&&ho(e,t)})(e,i,n||t);const r=s?function(e,t){const n=e.type;e.accessCache=Object.create(null),e.proxy=new Proxy(e.ctx,Dn);const{setup:o}=n;if(o){ve();const n=e.setupContext=o.length>1?function(e){const t=t=>{e.exposed=t||{}};return{attrs:new Proxy(e.attrs,Ci),slots:e.slots,emit:e.emit,expose:t}}(e):null,i=mi(e),s=Lt(o,e,0,[e.props,n]),r=y(s);if(ye(),i(),!r&&!e.sp||on(e)||tn(e),r){if(s.then(vi,vi),t)return s.then((t=>{bi(e,t)})).catch((t=>{Ot(t,e,0)}));e.asyncDep=s}else bi(e,s)}else _i(e)}(e,t):void 0;t&&fi(!1)}(a,!1,l),a.asyncDep){if(s&&s.registerDep(a,U,l),!e.el){const e=a.subTree=ei(Bo);C(null,e,n,o)}}else U(a,e,n,o,s,r,l)},I=(e,t,n)=>{const o=t.component=e.component;if(function(e,t,n){const{props:o,children:i,component:s}=e,{props:r,children:l,patchFlag:a}=t,c=s.emitsOptions;if(t.dirs||t.transition)return!0;if(!(n&&a>=0))return!(!i&&!l||l&&l.$stable)||o!==r&&(o?!r||Ro(o,r,c):!!r);if(1024&a)return!0;if(16&a)return o?Ro(o,r,c):!!r;if(8&a){const e=t.dynamicProps;for(let t=0;t<e.length;t++){const n=e[t];if(r[n]!==o[n]&&!Mo(c,n))return!0}}return!1}(e,t,n)){if(o.asyncDep&&!o.asyncResolved)return void W(o,t,n);o.next=t,o.update()}else t.el=e.el,o.vnode=t},U=(e,t,n,o,i,s,r)=>{const l=()=>{if(e.isMounted){let{next:t,bu:n,u:o,parent:a,vnode:c}=e;{const n=bo(e);if(n)return t&&(t.el=c.el,W(e,t,r)),void n.asyncDep.then((()=>{e.isUnmounted||l()}))}let u,d=t;yo(e,!1),t?(t.el=c.el,W(e,t,r)):t=c,n&&D(n),(u=t.props&&t.props.onVnodeBeforeUpdate)&&ui(u,a,t,c),yo(e,!0);const p=Ao(e),h=e.subTree;e.subTree=p,w(h,p,f(h.el),oe(h),e,i,s),t.el=p.el,null===d&&function({vnode:e,parent:t},n){for(;t;){const o=t.subTree;if(o.suspense&&o.suspense.activeBranch===e&&(o.el=e.el),o!==e)break;(e=t.vnode).el=n,t=t.parent}}(e,p.el),o&&go(o,i),(u=t.props&&t.props.onVnodeUpdated)&&go((()=>ui(u,a,t,c)),i)}else{let r;const{el:l,props:a}=t,{bm:c,m:u,parent:d,root:p,type:h}=e,f=on(t);yo(e,!1),c&&D(c),!f&&(r=a&&a.onVnodeBeforeMount)&&ui(r,d,t),yo(e,!0);{p.ce&&p.ce._injectChildStyle(h);const r=e.subTree=Ao(e);w(null,r,n,o,e,i,s),t.el=r.el}if(u&&go(u,i),!f&&(r=a&&a.onVnodeMounted)){const e=t;go((()=>ui(r,d,e)),i)}(256&t.shapeFlag||d&&on(d.vnode)&&256&d.vnode.shapeFlag)&&e.a&&go(e.a,i),e.isMounted=!0,t=n=o=null}};e.scope.on();const a=e.effect=new ne(l);e.scope.off();const c=e.update=a.run.bind(a),u=e.job=a.runIfDirty.bind(a);u.i=e,u.id=e.uid,a.scheduler=()=>Bt(u),yo(e,!0),c()},W=(e,n,o)=>{n.component=e;const i=e.vnode.props;e.vnode=n,e.next=null,function(e,t,n,o){const{props:i,attrs:s,vnode:{patchFlag:r}}=e,l=ft(i),[a]=e.propsOptions;let c=!1;if(!(o||r>0)||16&r){let o;oo(e,t,i,s)&&(c=!0);for(const s in l)t&&(u(t,s)||(o=O(s))!==s&&u(t,o))||(a?!n||void 0===n[s]&&void 0===n[o]||(i[s]=io(a,l,s,void 0,e,!0)):delete i[s]);if(s!==l)for(const e in s)t&&u(t,e)||(delete s[e],c=!0)}else if(8&r){const n=e.vnode.dynamicProps;for(let o=0;o<n.length;o++){let r=n[o];if(Mo(e.emitsOptions,r))continue;const d=t[r];if(a)if(u(s,r))d!==s[r]&&(s[r]=d,c=!0);else{const t=L(r);i[t]=io(a,l,t,d,e,!1)}else d!==s[r]&&(s[r]=d,c=!0)}}c&&Pe(e.attrs,"set","")}(e,n.props,i,o),((e,n,o)=>{const{vnode:i,slots:s}=e;let r=!0,l=t;if(32&i.shapeFlag){const e=n._;e?o&&1===e?r=!1:fo(s,n,o):(r=!n.$stable,po(n,s)),l=n}else n&&(ho(e,n),l={default:1});if(r)for(const t in s)ao(t)||null!=l[t]||delete s[t]})(e,n.children,o),ve(),Ft(e),ye()},Z=(e,t,n,o,i,s,r,l,a=!1)=>{const c=e&&e.children,u=e?e.shapeFlag:0,d=t.children,{patchFlag:p,shapeFlag:f}=t;if(p>0){if(128&p)return void G(c,d,n,o,i,s,r,l,a);if(256&p)return void q(c,d,n,o,i,s,r,l,a)}8&f?(16&u&&te(c,i,s),d!==c&&h(n,d)):16&u?16&f?G(c,d,n,o,i,s,r,l,a):te(c,i,s,!0):(8&u&&h(n,""),16&f&&E(d,n,o,i,s,r,l,a))},q=(e,t,o,i,s,r,l,a,c)=>{t=t||n;const u=(e=e||n).length,d=t.length,p=Math.min(u,d);let h;for(h=0;h<p;h++){const n=t[h]=c?li(t[h]):ri(t[h]);w(e[h],n,o,null,s,r,l,a,c)}u>d?te(e,s,r,!0,!1,p):E(t,o,i,s,r,l,a,c,p)},G=(e,t,o,i,s,r,l,a,c)=>{let u=0;const d=t.length;let p=e.length-1,h=d-1;for(;u<=p&&u<=h;){const n=e[u],i=t[u]=c?li(t[u]):ri(t[u]);if(!Ko(n,i))break;w(n,i,o,null,s,r,l,a,c),u++}for(;u<=p&&u<=h;){const n=e[p],i=t[h]=c?li(t[h]):ri(t[h]);if(!Ko(n,i))break;w(n,i,o,null,s,r,l,a,c),p--,h--}if(u>p){if(u<=h){const e=h+1,n=e<d?t[e].el:i;for(;u<=h;)w(null,t[u]=c?li(t[u]):ri(t[u]),o,n,s,r,l,a,c),u++}}else if(u>h)for(;u<=p;)K(e[u],s,r,!0),u++;else{const f=u,g=u,m=new Map;for(u=g;u<=h;u++){const e=t[u]=c?li(t[u]):ri(t[u]);null!=e.key&&m.set(e.key,u)}let v,y=0;const b=h-g+1;let C=!1,x=0;const S=new Array(b);for(u=0;u<b;u++)S[u]=0;for(u=f;u<=p;u++){const n=e[u];if(y>=b){K(n,s,r,!0);continue}let i;if(null!=n.key)i=m.get(n.key);else for(v=g;v<=h;v++)if(0===S[v-g]&&Ko(n,t[v])){i=v;break}void 0===i?K(n,s,r,!0):(S[i-g]=u+1,i>=x?x=i:C=!0,w(n,t[i],o,null,s,r,l,a,c),y++)}const k=C?function(e){const t=e.slice(),n=[0];let o,i,s,r,l;const a=e.length;for(o=0;o<a;o++){const a=e[o];if(0!==a){if(i=n[n.length-1],e[i]<a){t[o]=i,n.push(o);continue}for(s=0,r=n.length-1;s<r;)l=s+r>>1,e[n[l]]<a?s=l+1:r=l;a<e[n[s]]&&(s>0&&(t[o]=n[s-1]),n[s]=o)}}s=n.length,r=n[s-1];for(;s-- >0;)n[s]=r,r=t[r];return n}(S):n;for(v=k.length-1,u=b-1;u>=0;u--){const e=g+u,n=t[e],p=e+1<d?t[e+1].el:i;0===S[u]?w(null,n,o,p,s,r,l,a,c):C&&(v<0||u!==k[v]?Y(n,o,p,2):v--)}}},Y=(e,t,n,o,r=null)=>{const{el:l,type:a,transition:c,children:u,shapeFlag:d}=e;if(6&d)return void Y(e.component.subTree,t,n,o);if(128&d)return void e.suspense.move(t,n,o);if(64&d)return void a.move(e,t,n,re);if(a===jo){i(l,t,n);for(let e=0;e<u.length;e++)Y(u[e],t,n,o);return void i(e.anchor,t,n)}if(a===No)return void k(e,t,n);if(2!==o&&1&d&&c)if(0===o)c.beforeEnter(l),i(l,t,n),go((()=>c.enter(l)),r);else{const{leave:o,delayLeave:r,afterLeave:a}=c,u=()=>{e.ctx.isUnmounted?s(l):i(l,t,n)},d=()=>{o(l,(()=>{u(),a&&a()}))};r?r(l,u,d):d()}else i(l,t,n)},K=(e,t,n,o=!1,i=!1)=>{const{type:s,props:r,ref:l,children:a,dynamicChildren:c,shapeFlag:u,patchFlag:d,dirs:p,cacheIndex:h}=e;if(-2===d&&(i=!1),null!=l&&(ve(),nn(l,null,n,e,!0),ye()),null!=h&&(t.renderCache[h]=void 0),256&u)return void t.ctx.deactivate(e);const f=1&u&&p,g=!on(e);let m;if(g&&(m=r&&r.onVnodeBeforeUnmount)&&ui(m,t,e),6&u)J(e.component,n,o);else{if(128&u)return void e.suspense.unmount(n,o);f&&Xt(e,null,t,"beforeUnmount"),64&u?e.type.remove(e,t,n,re,o):c&&!c.hasOnce&&(s!==jo||d>0&&64&d)?te(c,t,n,!1,!0):(s===jo&&384&d||!i&&16&u)&&te(a,t,n),o&&X(e)}(g&&(m=r&&r.onVnodeUnmounted)||f)&&go((()=>{m&&ui(m,t,e),f&&Xt(e,null,t,"unmounted")}),n)},X=e=>{const{type:t,el:n,anchor:o,transition:i}=e;if(t===jo)return void Q(n,o);if(t===No)return void T(e);const r=()=>{s(n),i&&!i.persisted&&i.afterLeave&&i.afterLeave()};if(1&e.shapeFlag&&i&&!i.persisted){const{leave:t,delayLeave:o}=i,s=()=>t(n,r);o?o(e.el,r,s):s()}else r()},Q=(e,t)=>{let n;for(;e!==t;)n=g(e),s(e),e=n;s(t)},J=(e,t,n)=>{const{bum:o,scope:i,job:s,subTree:r,um:l,m:a,a:c,parent:u,slots:{__:p}}=e;_o(a),_o(c),o&&D(o),u&&d(p)&&p.forEach((e=>{u.renderCache[e]=void 0})),i.stop(),s&&(s.flags|=8,K(r,e,t,n)),l&&go(l,t),go((()=>{e.isUnmounted=!0}),t),t&&t.pendingBranch&&!t.isUnmounted&&e.asyncDep&&!e.asyncResolved&&e.suspenseId===t.pendingId&&(t.deps--,0===t.deps&&t.resolve())},te=(e,t,n,o=!1,i=!1,s=0)=>{for(let r=s;r<e.length;r++)K(e[r],t,n,o,i)},oe=e=>{if(6&e.shapeFlag)return oe(e.component.subTree);if(128&e.shapeFlag)return e.suspense.next();const t=g(e.anchor||e.el),n=t&&t[Qt];return n?g(n):t};let ie=!1;const se=(e,t,n)=>{null==e?t._vnode&&K(t._vnode,null,null,!0):w(t._vnode||null,e,t,null,null,null,n),t._vnode=e,ie||(ie=!0,Ft(),Vt(),ie=!1)},re={p:w,um:K,m:Y,r:X,mt:V,mc:E,pc:Z,pbc:j,n:oe,o:e};let le;return{render:se,hydrate:le,createApp:Kn(se)}}(e)}function vo({type:e,props:t},n){return"svg"===n&&"foreignObject"===e||"mathml"===n&&"annotation-xml"===e&&t&&t.encoding&&t.encoding.includes("html")?void 0:n}function yo({effect:e,job:t},n){n?(e.flags|=32,t.flags|=4):(e.flags&=-33,t.flags&=-5)}function wo(e,t,n=!1){const o=e.children,i=t.children;if(d(o)&&d(i))for(let s=0;s<o.length;s++){const e=o[s];let t=i[s];1&t.shapeFlag&&!t.dynamicChildren&&((t.patchFlag<=0||32===t.patchFlag)&&(t=i[s]=li(i[s]),t.el=e.el),n||-2===t.patchFlag||wo(e,t)),t.type===zo&&(t.el=e.el),t.type!==Bo||t.el||(t.el=e.el)}}function bo(e){const t=e.subTree.component;if(t)return t.asyncDep&&!t.asyncResolved?t:bo(t)}function _o(e){if(e)for(let t=0;t<e.length;t++)e[t].flags|=8}const Co=Symbol.for("v-scx"),xo=()=>Qn(Co);function So(e,t,n){return ko(e,t,n)}function ko(e,n,i=t){const{immediate:s,deep:r,flush:a,once:c}=i,u=l({},i),d=n&&s||!n&&"post"!==a;let p;if(wi)if("sync"===a){const e=xo();p=e.__watcherHandles||(e.__watcherHandles=[])}else if(!d){const e=()=>{};return e.stop=o,e.resume=o,e.pause=o,e}const h=gi;u.call=(e,t,n)=>Pt(e,h,t,n);let f=!1;"post"===a?u.scheduler=e=>{go(e,h&&h.suspense)}:"sync"!==a&&(f=!0,u.scheduler=(e,t)=>{t?e():Bt(e)}),u.augmentJob=e=>{n&&(e.flags|=4),f&&(e.flags|=2,h&&(e.id=h.uid,e.i=h))};const g=Tt(e,n,u);return wi&&(p?p.push(g):d&&g()),g}function To(e,t,n){const o=this.proxy,i=g(e)?e.includes(".")?$o(o,e):()=>o[e]:e.bind(o,o);let s;f(t)?s=t:(s=t.handler,n=t);const r=mi(this),l=ko(i,s.bind(o),n);return r(),l}function $o(e,t){const n=t.split(".");return()=>{let t=e;for(let e=0;e<n.length&&t;e++)t=t[n[e]];return t}}const Lo=(e,t)=>"modelValue"===t||"model-value"===t?e.modelModifiers:e[`${t}Modifiers`]||e[`${L(t)}Modifiers`]||e[`${O(t)}Modifiers`];function Po(e,n,...o){if(e.isUnmounted)return;const i=e.vnode.props||t;let s=o;const r=n.startsWith("update:"),l=r&&Lo(i,n.slice(7));let a;l&&(l.trim&&(s=o.map((e=>g(e)?e.trim():e))),l.number&&(s=o.map(H)));let c=i[a=A(n)]||i[a=A(L(n))];!c&&r&&(c=i[a=A(O(n))]),c&&Pt(c,e,6,s);const u=i[a+"Once"];if(u){if(e.emitted){if(e.emitted[a])return}else e.emitted={};e.emitted[a]=!0,Pt(u,e,6,s)}}function Oo(e,t,n=!1){const o=t.emitsCache,i=o.get(e);if(void 0!==i)return i;const s=e.emits;let r={},a=!1;if(!f(e)){const o=e=>{const n=Oo(e,t,!0);n&&(a=!0,l(r,n))};!n&&t.mixins.length&&t.mixins.forEach(o),e.extends&&o(e.extends),e.mixins&&e.mixins.forEach(o)}return s||a?(d(s)?s.forEach((e=>r[e]=null)):l(r,s),v(e)&&o.set(e,r),r):(v(e)&&o.set(e,null),null)}function Mo(e,t){return!(!e||!s(t))&&(t=t.slice(2).replace(/Once$/,""),u(e,t[0].toLowerCase()+t.slice(1))||u(e,O(t))||u(e,t))}function Ao(e){const{type:t,vnode:n,proxy:o,withProxy:i,propsOptions:[s],slots:l,attrs:a,emit:c,render:u,renderCache:d,props:p,data:h,setupState:f,ctx:g,inheritAttrs:m}=e,v=qt(e);let y,w;try{if(4&n.shapeFlag){const e=i||o,t=e;y=ri(u.call(t,e,d,p,f,h,g)),w=a}else{const e=t;0,y=ri(e.length>1?e(p,{attrs:a,slots:l,emit:c}):e(p,null)),w=t.props?a:Eo(a)}}catch(C){Fo.length=0,Ot(C,e,1),y=ei(Bo)}let b=y;if(w&&!1!==m){const e=Object.keys(w),{shapeFlag:t}=b;e.length&&7&t&&(s&&e.some(r)&&(w=Do(w,s)),b=ni(b,w,!1,!0))}return n.dirs&&(b=ni(b,null,!1,!0),b.dirs=b.dirs?b.dirs.concat(n.dirs):n.dirs),n.transition&&Jt(b,n.transition),y=b,qt(v),y}const Eo=e=>{let t;for(const n in e)("class"===n||"style"===n||s(n))&&((t||(t={}))[n]=e[n]);return t},Do=(e,t)=>{const n={};for(const o in e)r(o)&&o.slice(9)in t||(n[o]=e[o]);return n};function Ro(e,t,n){const o=Object.keys(t);if(o.length!==Object.keys(e).length)return!0;for(let i=0;i<o.length;i++){const s=o[i];if(t[s]!==e[s]&&!Mo(n,s))return!0}return!1}const Ho=e=>e.__isSuspense;const jo=Symbol.for("v-fgt"),zo=Symbol.for("v-txt"),Bo=Symbol.for("v-cmt"),No=Symbol.for("v-stc"),Fo=[];let Vo=null;function Io(e=!1){Fo.push(Vo=e?null:[])}let Uo=1;function Wo(e,t=!1){Uo+=e,e<0&&Vo&&t&&(Vo.hasOnce=!0)}function Zo(e){return e.dynamicChildren=Uo>0?Vo||n:null,Fo.pop(),Vo=Fo[Fo.length-1]||null,Uo>0&&Vo&&Vo.push(e),e}function qo(e,t,n,o,i,s){return Zo(Jo(e,t,n,o,i,s,!0))}function Go(e,t,n,o,i){return Zo(ei(e,t,n,o,i,!0))}function Yo(e){return!!e&&!0===e.__v_isVNode}function Ko(e,t){return e.type===t.type&&e.key===t.key}const Xo=({key:e})=>null!=e?e:null,Qo=({ref:e,ref_key:t,ref_for:n})=>("number"==typeof e&&(e=""+e),null!=e?g(e)||vt(e)||f(e)?{i:Wt,r:e,k:t,f:!!n}:e:null);function Jo(e,t=null,n=null,o=0,i=null,s=(e===jo?0:1),r=!1,l=!1){const a={__v_isVNode:!0,__v_skip:!0,type:e,props:t,key:t&&Xo(t),ref:t&&Qo(t),scopeId:Zt,slotScopeIds:null,children:n,component:null,suspense:null,ssContent:null,ssFallback:null,dirs:null,transition:null,el:null,anchor:null,target:null,targetStart:null,targetAnchor:null,staticCount:0,shapeFlag:s,patchFlag:o,dynamicProps:i,dynamicChildren:null,appContext:null,ctx:Wt};return l?(ai(a,n),128&s&&e.normalize(a)):n&&(a.shapeFlag|=g(n)?8:16),Uo>0&&!r&&Vo&&(a.patchFlag>0||6&s)&&32!==a.patchFlag&&Vo.push(a),a}const ei=function(e,t=null,n=null,o=0,i=null,s=!1){e&&e!==Sn||(e=Bo);if(Yo(e)){const o=ni(e,t,!0);return n&&ai(o,n),Uo>0&&!s&&Vo&&(6&o.shapeFlag?Vo[Vo.indexOf(e)]=o:Vo.push(o)),o.patchFlag=-2,o}r=e,f(r)&&"__vccOpts"in r&&(e=e.__vccOpts);var r;if(t){t=ti(t);let{class:e,style:n}=t;e&&!g(e)&&(t.class=U(e)),v(n)&&(ht(n)&&!d(n)&&(n=l({},n)),t.style=B(n))}const a=g(e)?1:Ho(e)?128:(e=>e.__isTeleport)(e)?64:v(e)?4:f(e)?2:0;return Jo(e,t,n,o,i,a,s,!0)};function ti(e){return e?ht(e)||to(e)?l({},e):e:null}function ni(e,t,n=!1,o=!1){const{props:i,ref:s,patchFlag:r,children:l,transition:a}=e,c=t?ci(i||{},t):i,u={__v_isVNode:!0,__v_skip:!0,type:e.type,props:c,key:c&&Xo(c),ref:t&&t.ref?n&&s?d(s)?s.concat(Qo(t)):[s,Qo(t)]:Qo(t):s,scopeId:e.scopeId,slotScopeIds:e.slotScopeIds,children:l,target:e.target,targetStart:e.targetStart,targetAnchor:e.targetAnchor,staticCount:e.staticCount,shapeFlag:e.shapeFlag,patchFlag:t&&e.type!==jo?-1===r?16:16|r:r,dynamicProps:e.dynamicProps,dynamicChildren:e.dynamicChildren,appContext:e.appContext,dirs:e.dirs,transition:a,component:e.component,suspense:e.suspense,ssContent:e.ssContent&&ni(e.ssContent),ssFallback:e.ssFallback&&ni(e.ssFallback),el:e.el,anchor:e.anchor,ctx:e.ctx,ce:e.ce};return a&&o&&Jt(u,a.clone(u)),u}function oi(e=" ",t=0){return ei(zo,null,e,t)}function ii(e,t){const n=ei(No,null,e);return n.staticCount=t,n}function si(e="",t=!1){return t?(Io(),Go(Bo,null,e)):ei(Bo,null,e)}function ri(e){return null==e||"boolean"==typeof e?ei(Bo):d(e)?ei(jo,null,e.slice()):Yo(e)?li(e):ei(zo,null,String(e))}function li(e){return null===e.el&&-1!==e.patchFlag||e.memo?e:ni(e)}function ai(e,t){let n=0;const{shapeFlag:o}=e;if(null==t)t=null;else if(d(t))n=16;else if("object"==typeof t){if(65&o){const n=t.default;return void(n&&(n._c&&(n._d=!1),ai(e,n()),n._c&&(n._d=!0)))}{n=32;const o=t._;o||to(t)?3===o&&Wt&&(1===Wt.slots._?t._=1:(t._=2,e.patchFlag|=1024)):t._ctx=Wt}}else f(t)?(t={default:t,_ctx:Wt},n=32):(t=String(t),64&o?(n=16,t=[oi(t)]):n=8);e.children=t,e.shapeFlag|=n}function ci(...e){const t={};for(let n=0;n<e.length;n++){const o=e[n];for(const e in o)if("class"===e)t.class!==o.class&&(t.class=U([t.class,o.class]));else if("style"===e)t.style=B([t.style,o.style]);else if(s(e)){const n=t[e],i=o[e];!i||n===i||d(n)&&n.includes(i)||(t[e]=n?[].concat(n,i):i)}else""!==e&&(t[e]=o[e])}return t}function ui(e,t,n,o=null){Pt(e,t,7,[n,o])}const di=Gn();let pi=0;let hi,fi,gi=null;{const e=z(),t=(t,n)=>{let o;return(o=e[t])||(o=e[t]=[]),o.push(n),e=>{o.length>1?o.forEach((t=>t(e))):o[0](e)}};hi=t("__VUE_INSTANCE_SETTERS__",(e=>gi=e)),fi=t("__VUE_SSR_SETTERS__",(e=>wi=e))}const mi=e=>{const t=gi;return hi(e),e.scope.on(),()=>{e.scope.off(),hi(t)}},vi=()=>{gi&&gi.scope.off(),hi(null)};function yi(e){return 4&e.vnode.shapeFlag}let wi=!1;function bi(e,t,n){f(t)?e.type.__ssrInlineRender?e.ssrRender=t:e.render=t:v(t)&&(e.setupState=_t(t)),_i(e)}function _i(e,t,n){const i=e.type;e.render||(e.render=i.render||o);{const t=mi(e);ve();try{jn(e)}finally{ye(),t()}}}const Ci={get:(e,t)=>(Le(e,0,""),e[t])};function xi(e){return e.exposed?e.exposeProxy||(e.exposeProxy=new Proxy(_t((t=e.exposed,!u(t,"__v_skip")&&Object.isExtensible(t)&&R(t,"__v_skip",!0),t)),{get:(t,n)=>n in t?t[n]:n in An?An[n](e):void 0,has:(e,t)=>t in e||t in An})):e.proxy;var t}function Si(e,t=!0){return f(e)?e.displayName||e.name:e.name||t&&e.__name}const ki=(e,t)=>{const n=function(e,t,n=!1){let o,i;return f(e)?o=e:(o=e.get,i=e.set),new Ct(o,i,n)}(e,0,wi);return n};const Ti="3.5.14";let $i;const Li="undefined"!=typeof window&&window.trustedTypes;if(Li)try{$i=Li.createPolicy("vue",{createHTML:e=>e})}catch(fd){}const Pi=$i?e=>$i.createHTML(e):e=>e,Oi="undefined"!=typeof document?document:null,Mi=Oi&&Oi.createElement("template"),Ai={insert:(e,t,n)=>{t.insertBefore(e,n||null)},remove:e=>{const t=e.parentNode;t&&t.removeChild(e)},createElement:(e,t,n,o)=>{const i="svg"===t?Oi.createElementNS("http://www.w3.org/2000/svg",e):"mathml"===t?Oi.createElementNS("http://www.w3.org/1998/Math/MathML",e):n?Oi.createElement(e,{is:n}):Oi.createElement(e);return"select"===e&&o&&null!=o.multiple&&i.setAttribute("multiple",o.multiple),i},createText:e=>Oi.createTextNode(e),createComment:e=>Oi.createComment(e),setText:(e,t)=>{e.nodeValue=t},setElementText:(e,t)=>{e.textContent=t},parentNode:e=>e.parentNode,nextSibling:e=>e.nextSibling,querySelector:e=>Oi.querySelector(e),setScopeId(e,t){e.setAttribute(t,"")},insertStaticContent(e,t,n,o,i,s){const r=n?n.previousSibling:t.lastChild;if(i&&(i===s||i.nextSibling))for(;t.insertBefore(i.cloneNode(!0),n),i!==s&&(i=i.nextSibling););else{Mi.innerHTML=Pi("svg"===o?`<svg>${e}</svg>`:"mathml"===o?`<math>${e}</math>`:e);const i=Mi.content;if("svg"===o||"mathml"===o){const e=i.firstChild;for(;e.firstChild;)i.appendChild(e.firstChild);i.removeChild(e)}t.insertBefore(i,n)}return[r?r.nextSibling:t.firstChild,n?n.previousSibling:t.lastChild]}},Ei=Symbol("_vtc");const Di=Symbol("_vod"),Ri=Symbol("_vsh"),Hi=Symbol(""),ji=/(^|;)\s*display\s*:/;const zi=/\s*!important$/;function Bi(e,t,n){if(d(n))n.forEach((n=>Bi(e,t,n)));else if(null==n&&(n=""),t.startsWith("--"))e.setProperty(t,n);else{const o=function(e,t){const n=Fi[t];if(n)return n;let o=L(t);if("filter"!==o&&o in e)return Fi[t]=o;o=M(o);for(let i=0;i<Ni.length;i++){const n=Ni[i]+o;if(n in e)return Fi[t]=n}return t}(e,t);zi.test(n)?e.setProperty(O(o),n.replace(zi,""),"important"):e[o]=n}}const Ni=["Webkit","Moz","ms"],Fi={};const Vi="http://www.w3.org/1999/xlink";function Ii(e,t,n,o,i,s=Z(t)){o&&t.startsWith("xlink:")?null==n?e.removeAttributeNS(Vi,t.slice(6,t.length)):e.setAttributeNS(Vi,t,n):null==n||s&&!q(n)?e.removeAttribute(t):e.setAttribute(t,s?"":m(n)?String(n):n)}function Ui(e,t,n,o,i){if("innerHTML"===t||"textContent"===t)return void(null!=n&&(e[t]="innerHTML"===t?Pi(n):n));const s=e.tagName;if("value"===t&&"PROGRESS"!==s&&!s.includes("-")){const o="OPTION"===s?e.getAttribute("value")||"":e.value,i=null==n?"checkbox"===e.type?"on":"":String(n);return o===i&&"_value"in e||(e.value=i),null==n&&e.removeAttribute(t),void(e._value=n)}let r=!1;if(""===n||null==n){const o=typeof e[t];"boolean"===o?n=q(n):null==n&&"string"===o?(n="",r=!0):"number"===o&&(n=0,r=!0)}try{e[t]=n}catch(fd){}r&&e.removeAttribute(i||t)}function Wi(e,t,n,o){e.addEventListener(t,n,o)}const Zi=Symbol("_vei");function qi(e,t,n,o,i=null){const s=e[Zi]||(e[Zi]={}),r=s[t];if(o&&r)r.value=o;else{const[n,l]=function(e){let t;if(Gi.test(e)){let n;for(t={};n=e.match(Gi);)e=e.slice(0,e.length-n[0].length),t[n[0].toLowerCase()]=!0}const n=":"===e[2]?e.slice(3):O(e.slice(2));return[n,t]}(t);if(o){const r=s[t]=function(e,t){const n=e=>{if(e._vts){if(e._vts<=n.attached)return}else e._vts=Date.now();Pt(function(e,t){if(d(t)){const n=e.stopImmediatePropagation;return e.stopImmediatePropagation=()=>{n.call(e),e._stopped=!0},t.map((e=>t=>!t._stopped&&e&&e(t)))}return t}(e,n.value),t,5,[e])};return n.value=e,n.attached=Xi(),n}(o,i);Wi(e,n,r,l)}else r&&(!function(e,t,n,o){e.removeEventListener(t,n,o)}(e,n,r,l),s[t]=void 0)}}const Gi=/(?:Once|Passive|Capture)$/;let Yi=0;const Ki=Promise.resolve(),Xi=()=>Yi||(Ki.then((()=>Yi=0)),Yi=Date.now());const Qi=e=>111===e.charCodeAt(0)&&110===e.charCodeAt(1)&&e.charCodeAt(2)>96&&e.charCodeAt(2)<123;const Ji=e=>{const t=e.props["onUpdate:modelValue"]||!1;return d(t)?e=>D(t,e):t};function es(e){e.target.composing=!0}function ts(e){const t=e.target;t.composing&&(t.composing=!1,t.dispatchEvent(new Event("input")))}const ns=Symbol("_assign"),os={created(e,{modifiers:{lazy:t,trim:n,number:o}},i){e[ns]=Ji(i);const s=o||i.props&&"number"===i.props.type;Wi(e,t?"change":"input",(t=>{if(t.target.composing)return;let o=e.value;n&&(o=o.trim()),s&&(o=H(o)),e[ns](o)})),n&&Wi(e,"change",(()=>{e.value=e.value.trim()})),t||(Wi(e,"compositionstart",es),Wi(e,"compositionend",ts),Wi(e,"change",ts))},mounted(e,{value:t}){e.value=null==t?"":t},beforeUpdate(e,{value:t,oldValue:n,modifiers:{lazy:o,trim:i,number:s}},r){if(e[ns]=Ji(r),e.composing)return;const l=null==t?"":t;if((!s&&"number"!==e.type||/^0\d/.test(e.value)?e.value:H(e.value))!==l){if(document.activeElement===e&&"range"!==e.type){if(o&&t===n)return;if(i&&e.value.trim()===l)return}e.value=l}}},is=["ctrl","shift","alt","meta"],ss={stop:e=>e.stopPropagation(),prevent:e=>e.preventDefault(),self:e=>e.target!==e.currentTarget,ctrl:e=>!e.ctrlKey,shift:e=>!e.shiftKey,alt:e=>!e.altKey,meta:e=>!e.metaKey,left:e=>"button"in e&&0!==e.button,middle:e=>"button"in e&&1!==e.button,right:e=>"button"in e&&2!==e.button,exact:(e,t)=>is.some((n=>e[`${n}Key`]&&!t.includes(n)))},rs=(e,t)=>{const n=e._withMods||(e._withMods={}),o=t.join(".");return n[o]||(n[o]=(n,...o)=>{for(let e=0;e<t.length;e++){const o=ss[t[e]];if(o&&o(n,t))return}return e(n,...o)})},ls={esc:"escape",space:" ",up:"arrow-up",left:"arrow-left",right:"arrow-right",down:"arrow-down",delete:"backspace"},as=(e,t)=>{const n=e._withKeys||(e._withKeys={}),o=t.join(".");return n[o]||(n[o]=n=>{if(!("key"in n))return;const o=O(n.key);return t.some((e=>e===o||ls[e]===o))?e(n):void 0})},cs=l({patchProp:(e,t,n,o,i,l)=>{const a="svg"===i;"class"===t?function(e,t,n){const o=e[Ei];o&&(t=(t?[t,...o]:[...o]).join(" ")),null==t?e.removeAttribute("class"):n?e.setAttribute("class",t):e.className=t}(e,o,a):"style"===t?function(e,t,n){const o=e.style,i=g(n);let s=!1;if(n&&!i){if(t)if(g(t))for(const e of t.split(";")){const t=e.slice(0,e.indexOf(":")).trim();null==n[t]&&Bi(o,t,"")}else for(const e in t)null==n[e]&&Bi(o,e,"");for(const e in n)"display"===e&&(s=!0),Bi(o,e,n[e])}else if(i){if(t!==n){const e=o[Hi];e&&(n+=";"+e),o.cssText=n,s=ji.test(n)}}else t&&e.removeAttribute("style");Di in e&&(e[Di]=s?o.display:"",e[Ri]&&(o.display="none"))}(e,n,o):s(t)?r(t)||qi(e,t,0,o,l):("."===t[0]?(t=t.slice(1),1):"^"===t[0]?(t=t.slice(1),0):function(e,t,n,o){if(o)return"innerHTML"===t||"textContent"===t||!!(t in e&&Qi(t)&&f(n));if("spellcheck"===t||"draggable"===t||"translate"===t||"autocorrect"===t)return!1;if("form"===t)return!1;if("list"===t&&"INPUT"===e.tagName)return!1;if("type"===t&&"TEXTAREA"===e.tagName)return!1;if("width"===t||"height"===t){const t=e.tagName;if("IMG"===t||"VIDEO"===t||"CANVAS"===t||"SOURCE"===t)return!1}if(Qi(t)&&g(n))return!1;return t in e}(e,t,o,a))?(Ui(e,t,o),e.tagName.includes("-")||"value"!==t&&"checked"!==t&&"selected"!==t||Ii(e,t,o,a,0,"value"!==t)):!e._isVueCE||!/[A-Z]/.test(t)&&g(o)?("true-value"===t?e._trueValue=o:"false-value"===t&&(e._falseValue=o),Ii(e,t,o,a)):Ui(e,L(t),o,0,t)}},Ai);let us;const ds=(...e)=>{const t=(us||(us=mo(cs))).createApp(...e),{mount:n}=t;return t.mount=e=>{const o=function(e){if(g(e)){return document.querySelector(e)}return e}(e);if(!o)return;const i=t._component;f(i)||i.render||i.template||(i.template=o.innerHTML),1===o.nodeType&&(o.textContent="");const s=n(o,!1,function(e){if(e instanceof SVGElement)return"svg";if("function"==typeof MathMLElement&&e instanceof MathMLElement)return"mathml"}(o));return o instanceof Element&&(o.removeAttribute("v-cloak"),o.setAttribute("data-v-app","")),s},t};function ps(e){throw`facing-metadata: ${e}`}var hs,fs,gs=function(e,t,n,o){if("a"===n&&!o)throw new TypeError("Private accessor was defined without a getter");if("function"==typeof t?e!==t||!o:!t.has(e))throw new TypeError("Cannot read private member from an object whose class did not declare it");return"m"===n?o:"a"===n?o.call(e):o?o.value:t.get(e)};hs=new WeakSet,fs=function(e,t){let n=e;do{const o=this.getOwn(n);if(void 0!==o&&!t(e,o))break;n=Object.getPrototypeOf(n)}while(null!==n)};const ms=Symbol("vue-facing-decorator-slot");class vs{constructor(e){Object.defineProperty(this,"master",{enumerable:!0,configurable:!0,writable:!0,value:void 0}),Object.defineProperty(this,"names",{enumerable:!0,configurable:!0,writable:!0,value:new Map}),Object.defineProperty(this,"inComponent",{enumerable:!0,configurable:!0,writable:!0,value:!1}),Object.defineProperty(this,"cachedVueComponent",{enumerable:!0,configurable:!0,writable:!0,value:null}),this.master=e}obtainMap(e){let t=this.getMap(e);return t||(t=new Map,this.names.set(e,t)),t}getMap(e){return this.names.get(e)}}const ys=new class{constructor(e=Symbol("faple-metadata")){hs.add(this),this.symbol=e}create(e,t){Object.getOwnPropertyDescriptor(e,this.symbol)&&ps("Target had metadata"),Object.defineProperty(e,this.symbol,{enumerable:!1,configurable:!1,writable:!1,value:t})}getOwn(e){const t=Object.getOwnPropertyDescriptor(e,this.symbol);if(t)return t.value}get(e){let t;return gs(this,hs,"m",fs).call(this,e,((e,n)=>(t=n,!1))),t}getAll(e){let t=[];return gs(this,hs,"m",fs).call(this,e,((e,n)=>(t.push(n),!0))),t}setValueOwn(e,t,n){const o=this.getOwn(e);o||ps("Target has not metadata"),o[t]=n}setValue(e,t,n){const o=this.get(e);o||ps("Target has not metadata"),o[t]=n}getValueOwn(e,t){const n=this.getOwn(e);return void 0===n&&ps("Target has not metadata"),n[t]}getValue(e,t){let n,o=!1;return gs(this,hs,"m",fs).call(this,e,((e,i)=>(o=!0,!(t in i)||void 0===i[t]||(n=i[t],!1)))),o||ps("Target has not metadata"),n}}(ms);function ws(e){return ys.getOwn(e)}function bs(e,t){const n=ws(e);return n||function(e,t){if(ws(e))throw"";t&&(t.master=e);const n=null!=t?t:new vs(e);return ys.create(e,n),n}(e,t)}const _s={};function Cs(e){return function(t,n){var o;if(n){if("class"!==n.kind)throw"deco stage 3 class";const i=bs(null!==(o=_s.fakePrototype)&&void 0!==o?o:_s.fakePrototype={});delete _s.fakePrototype,bs(t.prototype,i);return e(t)}return e(t)}}function xs(e){return function(t,n){var o;if("object"==typeof n){const i=n,s=t,r=null!==(o=_s.fakePrototype)&&void 0!==o?o:_s.fakePrototype={};return r[i.name]=s,e(r,i.name)}return e(t,n)}}const Ss=class{};function ks(e){const t=Object.getPrototypeOf(e);return t instanceof Ss?t:null}function Ts(e){const t=[];let n=e;do{t.unshift(n),n=ks(n)}while(null!==n&&!ws(n));return t}function $s(e){let t=ks(e);for(;null!==t;){const e=ws(t);if(e)return e;t=ks(t)}return null}function Ls(e,t,n){return e.filter((e=>{let o=t;for(;null!=o;){for(const t of o.names.keys()){if("customDecorator"===t){const t=o.obtainMap("customDecorator");if(t.has(e)&&t.get(e).every((e=>!e.preserve)))return!1}if(n&&n.includes(t))continue;if(o.names.get(t).has(e))return!1}o=$s(o.master)}return!0}))}function Ps(e,t){const n=Object.getOwnPropertyDescriptors(e);return Object.keys(n).filter((e=>t(n[e],e)))}function Os(e){return"function"==typeof e?e:function(){return e||{}}}function Ms(e){return function(t,n){if(!n){const n=t;return xs((function(t,o){e(t,o,n)}))}{const o=t;xs((function(t,n){e(t,n)}))(o,n)}}}const As=["beforeCreate","created","beforeMount","mounted","beforeUpdate","updated","activated","deactivated","beforeDestroy","beforeUnmount","destroyed","unmounted","renderTracked","renderTriggered","errorCaptured","serverPrefetch","render"];function Es(e,t){var n;null!==(n=e.beforeCreateCallbacks)&&void 0!==n||(e.beforeCreateCallbacks=[]),e.beforeCreateCallbacks.push((function(){const e=this;t(e).forEach(((t,n)=>{Object.defineProperty(e,n,t)}))}))}const Ds=Ms((function(e,t,n){bs(e).obtainMap("ref").set(t,void 0===n?null:n)}));const Rs=Ms((function(e,t,n){const o=bs(e).obtainMap("props"),i=Object.assign({},null!=n?n:{});o.set(t,i)}));var Hs=function(e,t,n,o){return new(n||(n=Promise))((function(i,s){function r(e){try{a(o.next(e))}catch(fd){s(fd)}}function l(e){try{a(o.throw(e))}catch(fd){s(fd)}}function a(e){var t;e.done?i(e.value):(t=e.value,t instanceof n?t:new n((function(e){e(t)}))).then(r,l)}a((o=o.apply(e,t||[])).next())}))};function js(e,t){const n={};!function(e,t){const n=bs(e.prototype).getMap("setup");if(!n||0===n.size)return;t.setup=function(e,t){const o={};let i=null;for(const s of n.keys()){const r=n.get(s).setupFunction(e,t);r instanceof Promise?(null!=i||(i=[]),i.push(r.then((e=>{o[s]=e})))):o[s]=r}return Array.isArray(i)?Promise.all(i).then((()=>o)):o}}(e,n),function(e,t){var n;null!==(n=t.computed)&&void 0!==n||(t.computed={});const o=bs(e.prototype),i=o.getMap("v-model");if(!i||0===i.size)return;const s=o.obtainMap("emits");i.forEach(((e,n)=>{var o;const i=null!==(o=e&&e.name)&&void 0!==o?o:"modelValue",r=`update:${i}`;t.computed[n]={get:function(){return this[i]},set:function(e){this.$emit(r,e)}},s.set(r,!0)}))}(e,n),function(e,t){var n;null!==(n=t.computed)&&void 0!==n||(t.computed={});const o=bs(e.prototype),i=o.obtainMap("computed"),s=o.obtainMap("vanilla");Ts(e.prototype).forEach((e=>{Ps(e,((e,t)=>("function"==typeof e.get||"function"==typeof e.set)&&!s.has(t))).forEach((n=>{i.set(n,!0);const o=Object.getOwnPropertyDescriptor(e,n);t.computed[n]={get:"function"==typeof o.get?o.get:void 0,set:"function"==typeof o.set?o.set:void 0}}))}))}(e,n),function(e,t){var n;null!==(n=t.watch)&&void 0!==n||(t.watch={});const o=bs(e.prototype).getMap("watch");o&&0!==o.size&&o.forEach(((e,n)=>{(Array.isArray(e)?e:[e]).forEach((e=>{if(t.watch[e.key]){const n=t.watch[e.key];Array.isArray(n)?n.push(e):t.watch[e.key]=[n,e]}else t.watch[e.key]=e}))}))}(e,n),function(e,t){var n;null!==(n=t.props)&&void 0!==n||(t.props={});const o=bs(e.prototype).getMap("props");o&&0!==o.size&&o.forEach(((e,n)=>{t.props[n]=e}))}(e,n),function(e,t){var n;null!==(n=t.inject)&&void 0!==n||(t.inject={});const o=bs(e.prototype).getMap("inject");o&&0!==o.size&&o.forEach(((e,n)=>{t.inject[n]=e}))}(e,n),function(e,t){var n;null!==(n=t.methods)&&void 0!==n||(t.methods={});const o=e.prototype,i=bs(o),s=i.getMap("emit");if(!s||0===s.size)return;const r=i.obtainMap("emits");s.forEach(((e,n)=>{const i=null===e?n:e;r.set(i,!0),t.methods[n]=function(){return Hs(this,arguments,void 0,(function*(){const e=o[n].apply(this,arguments);if(e instanceof Promise){const t=yield e;this.$emit(i,t)}else void 0===e?this.$emit(i):this.$emit(i,e)}))}}))}(e,n),function(e,t){const n=bs(e.prototype).getMap("ref");n&&0!==n.size&&Es(t,(e=>{const t=new Map;return n.forEach(((n,o)=>{const i=null===n?o:n;t.set(o,{get:function(){return e.$refs[i]},set:void 0})})),t}))}(e,n),function(e,t){const n=bs(e.prototype).getMap("vanilla");if(!n||0===n.size)return;const o=Ts(e.prototype),i=new Map;Es(t,(e=>(o.forEach((t=>{const o=Object.getOwnPropertyDescriptors(t);for(const s in o){const t=o[s];t&&n.has(s)&&("function"!=typeof t.get&&"function"!=typeof t.set||i.set(s,{set:"function"==typeof t.set?t.set.bind(e):void 0,get:"function"==typeof t.get?t.get.bind(e):void 0}))}})),i)))}(e,n),function(e,t){var n,o,i;const s=bs(e.prototype),r=Ts(e.prototype),l=s.obtainMap("hooks");null!==(n=t.hooks)&&void 0!==n||(t.hooks={}),null!==(o=t.methods)&&void 0!==o||(t.methods={});const a={},c={};r.forEach((e=>{let t=Ps(e,((e,t)=>"function"==typeof e.value&&"constructor"!==t));t=Ls(t,s,["watch","hooks","emits","provide","customDecorator"]),t.forEach((t=>{As.includes(t)||l.has(t)?a[t]=e[t]:c[t]=e[t]}))})),Object.assign(t.methods,c);const u=[...null!==(i=t.beforeCreateCallbacks)&&void 0!==i?i:[]];if(u&&u.length>0){const e=a.beforeCreate;a.beforeCreate=function(){u.forEach((e=>e.apply(this,arguments))),e&&e.apply(this,arguments)}}Object.assign(t.hooks,a)}(e,n);const o=Object.assign(Object.assign({name:e.name,setup:n.setup,data(){var t;return delete n.data,function(e,t){var n;null!==(n=t.data)&&void 0!==n||(t.data={});const o=new e;let i=Ps(o,((e,n)=>{var o,i;return!!e.enumerable&&!(null===(o=t.methods)||void 0===o?void 0:o[n])&&!(null===(i=t.props)||void 0===i?void 0:i[n])}));i=Ls(i,bs(e.prototype),["provide","customDecorator"]),Object.assign(t.data,i.reduce(((e,t)=>(e[t]=o[t],e)),{}))}(e,n),null!==(t=n.data)&&void 0!==t?t:{}},methods:n.methods,computed:n.computed,watch:n.watch,props:n.props,inject:n.inject,provide(){var t;return function(e,t,n){var o;null!==(o=t.provide)&&void 0!==o||(t.provide={});const i=bs(e.prototype).obtainMap("provide");if(!i)return null;i.forEach(((e,o)=>{const i=null===e?o:e;t.provide[i]=ki((()=>n[o]))}))}(e,n,this),null!==(t=n.provide)&&void 0!==t?t:{}}},n.hooks),{extends:t});return o}function zs(e,t){const n=bs(e.prototype);n.inComponent=!0;const o=$s(e.prototype);if(o){if(!o.inComponent)throw"Class should be decorated by Component or ComponentBase: "+n.master;if(null===o.cachedVueComponent)throw"Component decorator 1"}const i=function(e,t,n){var o,i;const s=js(e,n),r=bs(e.prototype);Object.keys(t).reduce(((e,n)=>(["options","modifier","methods","emits","setup","provide"].includes(n)||(e[n]=t[n]),e)),s);let l=Array.from(r.obtainMap("emits").keys());if(Array.isArray(t.emits)&&(l=Array.from(new Set([...l,...t.emits]))),s.emits=l,"object"!=typeof t.methods||Array.isArray(t.methods)||null===t.methods||(null!==(o=s.methods)&&void 0!==o||(s.methods={}),Object.assign(s.methods,t.methods)),s.setup){const e=s.setup,n=null!==(i=t.setup)&&void 0!==i?i:function(){return{}},o=function(t,o){const i=n(t,o),s=e(t,o);return s instanceof Promise||i instanceof Promise?Promise.all([i,s]).then((e=>Object.assign({},e[0],e[1]))):Object.assign({},i,s)};s.setup=o}else s.setup=t.setup;const a=Os(s.provide),c=Os(t.provide);s.provide=function(){return Object.assign({},a.call(this),c.call(this))};const u=r.getMap("customDecorator");return u&&u.size>0&&u.forEach((e=>{e.forEach((e=>e.creator.apply({},[s,e.key])))})),t.options&&Object.assign(s,t.options),t.modifier&&t.modifier(s),en(s)}(e,t,null===o?void 0:o.cachedVueComponent);i.__vfdConstructor=e,n.cachedVueComponent=i,e.__vccOpts=i}const Bs=(Ns=(e,t)=>{zs(e,null!=t?t:{})},function(e,t){if("function"!=typeof e){const t=e;return Cs((function(e){Ns(e,t)}))}{const n=e;Cs((function(e){Ns(e)}))(n,t)}});var Ns;function Fs(e){const t=bs(e.prototype);if(!t.inComponent)throw"to native 1";const n=t.cachedVueComponent;if(!n)throw"to native 2";return n}var Vs;const Is=lt({isActive:!1,emitter:{all:Vs=Vs||new Map,on:function(e,t){var n=Vs.get(e);n?n.push(t):Vs.set(e,[t])},off:function(e,t){var n=Vs.get(e);n&&(t?n.splice(n.indexOf(t)>>>0,1):Vs.set(e,[]))},emit:function(e,t){var n=Vs.get(e);n&&n.slice().map((function(e){e(t)})),(n=Vs.get("*"))&&n.slice().map((function(n){n(e,t)}))}},show(){this.isActive=!0,this.emitter.emit("show")},hide(){this.isActive=!1,this.emitter.emit("hide")}}),Us={mounted(e,t){if("object"!=typeof t.value)return void console.warn("v-drag expects a object with keys params, handleDrag as the directive value");let n,o,i;const s=t=>{e.setPointerCapture(t.pointerId),e.offsetLeft,e.offsetTop,o=t.clientX,i=t.clientY,n=!1,document.addEventListener("pointermove",r),document.addEventListener("pointerup",l),document.addEventListener("pointercancel",l),t.preventDefault()},r=e=>{const s=e.clientX-o,r=e.clientY-i;o=e.clientX,i=e.clientY,n=!0,t.value.handleDrag({x:s,y:r},t.value.params)},l=t=>{e.releasePointerCapture(t.pointerId),document.removeEventListener("pointermove",r),document.removeEventListener("pointerup",l),document.removeEventListener("pointercancel",l),n&&e.dispatchEvent(new CustomEvent("dragend",{bubbles:!0}))},a=e=>{n&&(e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation())};e.addEventListener("pointerdown",s),e.addEventListener("click",a),e._dragHandlers={onMouseDown:s,onMouseMove:r,onMouseUp:l,onClick:a}},unmounted(e){if(e._dragHandlers){const{onMouseDown:t,onMouseMove:n,onMouseUp:o,onClick:i}=e._dragHandlers;e.removeEventListener("pointerdown",t),e.removeEventListener("click",i),document.removeEventListener("pointermove",n),document.removeEventListener("pointerup",o),document.removeEventListener("pointercancel",o)}}};class Ws{constructor(){this.abortController=new AbortController}setStreamCallback(e){this.streamCallback=e}getAbortController(){return this.abortController}send(e,t={}){let n=window.global.ai.chatUrl+"?do="+e;return new Promise(((e,o)=>{try{return fetch(n,{method:"POST",signal:this.abortController.signal,headers:{"Content-Type":"application/x-www-form-urlencoded;charset=UTF-8","X-Requested-With":"XMLHttpRequest"},body:new URLSearchParams(t)}).then((t=>{if(!t.ok)throw new Error("Network response was not ok");e(this.fetchResponse(t))})).catch((e=>{o(e)}))}catch(i){o(i)}}))}async fetchResponse(e){if("application/stream+json"===e.headers.get("Content-type")){const n=e.body.getReader(),o=new TextDecoder;let i,s="",r="";for(;;){const{done:e,value:l}=await n.read();if(e)break;s+=o.decode(l,{stream:!0});const a=s.split("\n");s=a.pop();for(const n of a)if(""!==n.trim())try{i=JSON.parse(n),r+=i.text,this.streamCallback(r,i)}catch(t){console.log(t)}}return new Promise((e=>{e({fullText:r,lastChunk:i})}))}return e.json().then((e=>(window.$.rs.checkAuthorization(e),window.$.rs.checkWindowRedirect(e),window.$.rs.checkMessages(e),e)))}}const Zs=(e,t)=>{const n=e.__vccOpts||e;for(const[o,i]of t)n[o]=i;return n},qs={class:"ai-trigger-icon",height:"24",viewBox:"0 0 846.66 846.66",width:"24","xml:space":"preserve",xmlns:"http://www.w3.org/2000/svg"};const Gs=Zs({},[["render",function(e,t){return Io(),qo("svg",qs,t[0]||(t[0]=[Jo("path",{fill:"currentColor",d:"M652.73 649.32l0 166.42c0,18.73 -22.85,27.54 -35.55,14.29l-196.15 -180.71 -165.38 0c-51.56,0 -93.66,-42.1 -93.66,-93.66l0 -360.47c0,-51.55 42.1,-93.65 93.66,-93.65l469.97 0c51.56,0 93.66,42.09 93.66,93.65l0 360.47c0,51.56 -42.1,93.66 -93.66,93.66l-72.89 0zm-334.34 -322.96c19.53,0 35.36,15.83 35.36,35.36 0,19.53 -15.83,35.36 -35.36,35.36 -19.53,0 -35.36,-15.83 -35.36,-35.36 0,-19.53 15.83,-35.36 35.36,-35.36zm344.49 0c19.53,0 35.36,15.83 35.36,35.36 0,19.53 -15.83,35.36 -35.36,35.36 -19.53,0 -35.36,-15.83 -35.36,-35.36 0,-19.53 15.83,-35.36 35.36,-35.36zm-114.83 0c19.53,0 35.36,15.83 35.36,35.36 0,19.53 -15.83,35.36 -35.36,35.36 -19.53,0 -35.36,-15.83 -35.36,-35.36 0,-19.53 15.83,-35.36 35.36,-35.36zm-114.83 0c19.53,0 35.36,15.83 35.36,35.36 0,19.53 -15.83,35.36 -35.36,35.36 -19.53,0 -35.36,-15.83 -35.36,-35.36 0,-19.53 15.83,-35.36 35.36,-35.36zm-312.18 190.39c27.16,0 27.16,41.31 0,41.31 -51.56,0 -93.66,-42.1 -93.66,-93.66l0 -360.47c0,-51.55 42.1,-93.65 93.66,-93.65l469.98 0c27.16,0 27.16,41.3 0,41.3l-469.98 0c-28.75,0 -52.35,23.6 -52.35,52.35l0 360.47c0,28.75 23.6,52.35 52.35,52.35zm490.39 251.94l0 -140.03c0,-11.4 9.25,-20.65 20.65,-20.65l93.54 0c28.75,0 52.36,-23.6 52.36,-52.35l0 -360.47c0,-28.75 -23.61,-52.35 -52.36,-52.35l-469.97 0c-28.76,0 -52.36,23.6 -52.36,52.35l0 360.47c0,28.75 23.6,52.35 52.36,52.35l173.38 0.05c4.99,0 10,1.79 13.96,5.44l168.44 155.19z"},null,-1)]))}]]),Ys={class:"ai-trigger-back",width:"36",height:"99",viewBox:"0 0 36 99",fill:"none",xmlns:"http://www.w3.org/2000/svg"};const Ks=Zs({},[["render",function(e,t){return Io(),qo("svg",Ys,t[0]||(t[0]=[Jo("path",{d:"M16 24.75C32.4999 14 35.9993 0 35.9993 0V49.5V99C35.9993 99 32.9999 83.8186 16 74.25C-1.00001 64.6814 0.99979 49.5 0.99979 49.5C0.99979 49.5 -0.500009 35.5 16 24.75Z",fill:"url(#paint0_linear_473_78)"},null,-1),Jo("defs",null,[Jo("linearGradient",{id:"paint0_linear_473_78",x1:"1.24435e-06",y1:"97.8195",x2:"35.29",y2:"0.921951",gradientUnits:"userSpaceOnUse"},[Jo("stop",{"stop-color":"#64B2DB"}),Jo("stop",{offset:"1","stop-color":"#DFEAFF"})])],-1)]))}]]),Xs=["start","end"],Qs=["top","right","bottom","left"].reduce(((e,t)=>e.concat(t,t+"-"+Xs[0],t+"-"+Xs[1])),[]),Js=Math.min,er=Math.max,tr={left:"right",right:"left",bottom:"top",top:"bottom"},nr={start:"end",end:"start"};function or(e,t,n){return er(e,Js(t,n))}function ir(e,t){return"function"==typeof e?e(t):e}function sr(e){return e.split("-")[0]}function rr(e){return e.split("-")[1]}function lr(e){return"x"===e?"y":"x"}function ar(e){return"y"===e?"height":"width"}function cr(e){return["top","bottom"].includes(sr(e))?"y":"x"}function ur(e){return lr(cr(e))}function dr(e,t,n){void 0===n&&(n=!1);const o=rr(e),i=ur(e),s=ar(i);let r="x"===i?o===(n?"end":"start")?"right":"left":"start"===o?"bottom":"top";return t.reference[s]>t.floating[s]&&(r=hr(r)),[r,hr(r)]}function pr(e){return e.replace(/start|end/g,(e=>nr[e]))}function hr(e){return e.replace(/left|right|bottom|top/g,(e=>tr[e]))}function fr(e){return"number"!=typeof e?function(e){return{top:0,right:0,bottom:0,left:0,...e}}(e):{top:e,right:e,bottom:e,left:e}}function gr(e){const{x:t,y:n,width:o,height:i}=e;return{width:o,height:i,top:n,left:t,right:t+o,bottom:n+i,x:t,y:n}}function mr(e,t,n){let{reference:o,floating:i}=e;const s=cr(t),r=ur(t),l=ar(r),a=sr(t),c="y"===s,u=o.x+o.width/2-i.width/2,d=o.y+o.height/2-i.height/2,p=o[l]/2-i[l]/2;let h;switch(a){case"top":h={x:u,y:o.y-i.height};break;case"bottom":h={x:u,y:o.y+o.height};break;case"right":h={x:o.x+o.width,y:d};break;case"left":h={x:o.x-i.width,y:d};break;default:h={x:o.x,y:o.y}}switch(rr(t)){case"start":h[r]-=p*(n&&c?-1:1);break;case"end":h[r]+=p*(n&&c?-1:1)}return h}async function vr(e,t){var n;void 0===t&&(t={});const{x:o,y:i,platform:s,rects:r,elements:l,strategy:a}=e,{boundary:c="clippingAncestors",rootBoundary:u="viewport",elementContext:d="floating",altBoundary:p=!1,padding:h=0}=ir(t,e),f=fr(h),g=l[p?"floating"===d?"reference":"floating":d],m=gr(await s.getClippingRect({element:null==(n=await(null==s.isElement?void 0:s.isElement(g)))||n?g:g.contextElement||await(null==s.getDocumentElement?void 0:s.getDocumentElement(l.floating)),boundary:c,rootBoundary:u,strategy:a})),v="floating"===d?{x:o,y:i,width:r.floating.width,height:r.floating.height}:r.reference,y=await(null==s.getOffsetParent?void 0:s.getOffsetParent(l.floating)),w=await(null==s.isElement?void 0:s.isElement(y))&&await(null==s.getScale?void 0:s.getScale(y))||{x:1,y:1},b=gr(s.convertOffsetParentRelativeRectToViewportRelativeRect?await s.convertOffsetParentRelativeRectToViewportRelativeRect({elements:l,rect:v,offsetParent:y,strategy:a}):v);return{top:(m.top-b.top+f.top)/w.y,bottom:(b.bottom-m.bottom+f.bottom)/w.y,left:(m.left-b.left+f.left)/w.x,right:(b.right-m.right+f.right)/w.x}}const yr=function(e){return void 0===e&&(e={}),{name:"autoPlacement",options:e,async fn(t){var n,o,i;const{rects:s,middlewareData:r,placement:l,platform:a,elements:c}=t,{crossAxis:u=!1,alignment:d,allowedPlacements:p=Qs,autoAlignment:h=!0,...f}=ir(e,t),g=void 0!==d||p===Qs?function(e,t,n){return(e?[...n.filter((t=>rr(t)===e)),...n.filter((t=>rr(t)!==e))]:n.filter((e=>sr(e)===e))).filter((n=>!e||rr(n)===e||!!t&&pr(n)!==n))}(d||null,h,p):p,m=await vr(t,f),v=(null==(n=r.autoPlacement)?void 0:n.index)||0,y=g[v];if(null==y)return{};const w=dr(y,s,await(null==a.isRTL?void 0:a.isRTL(c.floating)));if(l!==y)return{reset:{placement:g[0]}};const b=[m[sr(y)],m[w[0]],m[w[1]]],C=[...(null==(o=r.autoPlacement)?void 0:o.overflows)||[],{placement:y,overflows:b}],x=g[v+1];if(x)return{data:{index:v+1,overflows:C},reset:{placement:x}};const S=C.map((e=>{const t=rr(e.placement);return[e.placement,t&&u?e.overflows.slice(0,2).reduce(((e,t)=>e+t),0):e.overflows[0],e.overflows]})).sort(((e,t)=>e[1]-t[1])),k=(null==(i=S.filter((e=>e[2].slice(0,rr(e[0])?2:3).every((e=>e<=0))))[0])?void 0:i[0])||S[0][0];return k!==l?{data:{index:v+1,overflows:C},reset:{placement:k}}:{}}}},wr=function(e){return void 0===e&&(e={}),{name:"flip",options:e,async fn(t){var n,o;const{placement:i,middlewareData:s,rects:r,initialPlacement:l,platform:a,elements:c}=t,{mainAxis:u=!0,crossAxis:d=!0,fallbackPlacements:p,fallbackStrategy:h="bestFit",fallbackAxisSideDirection:f="none",flipAlignment:g=!0,...m}=ir(e,t);if(null!=(n=s.arrow)&&n.alignmentOffset)return{};const v=sr(i),y=cr(l),w=sr(l)===l,b=await(null==a.isRTL?void 0:a.isRTL(c.floating)),C=p||(w||!g?[hr(l)]:function(e){const t=hr(e);return[pr(e),t,pr(t)]}(l)),x="none"!==f;!p&&x&&C.push(...function(e,t,n,o){const i=rr(e);let s=function(e,t,n){const o=["left","right"],i=["right","left"],s=["top","bottom"],r=["bottom","top"];switch(e){case"top":case"bottom":return n?t?i:o:t?o:i;case"left":case"right":return t?s:r;default:return[]}}(sr(e),"start"===n,o);return i&&(s=s.map((e=>e+"-"+i)),t&&(s=s.concat(s.map(pr)))),s}(l,g,f,b));const S=[l,...C],k=await vr(t,m),T=[];let L=(null==(o=s.flip)?void 0:o.overflows)||[];if(u&&T.push(k[v]),d){const e=dr(i,r,b);T.push(k[e[0]],k[e[1]])}if(L=[...L,{placement:i,overflows:T}],!T.every((e=>e<=0))){var P,O;const e=((null==(P=s.flip)?void 0:P.index)||0)+1,t=S[e];if(t){var M;const n="alignment"===d&&y!==cr(t),o=(null==(M=L[0])?void 0:M.overflows[0])>0;if(!n||o)return{data:{index:e,overflows:L},reset:{placement:t}}}let n=null==(O=L.filter((e=>e.overflows[0]<=0)).sort(((e,t)=>e.overflows[1]-t.overflows[1]))[0])?void 0:O.placement;if(!n)switch(h){case"bestFit":{var A;const e=null==(A=L.filter((e=>{if(x){const t=cr(e.placement);return t===y||"y"===t}return!0})).map((e=>[e.placement,e.overflows.filter((e=>e>0)).reduce(((e,t)=>e+t),0)])).sort(((e,t)=>e[1]-t[1]))[0])?void 0:A[0];e&&(n=e);break}case"initialPlacement":n=l}if(i!==n)return{reset:{placement:n}}}return{}}}};const br=function(e){return void 0===e&&(e=0),{name:"offset",options:e,async fn(t){var n,o;const{x:i,y:s,placement:r,middlewareData:l}=t,a=await async function(e,t){const{placement:n,platform:o,elements:i}=e,s=await(null==o.isRTL?void 0:o.isRTL(i.floating)),r=sr(n),l=rr(n),a="y"===cr(n),c=["left","top"].includes(r)?-1:1,u=s&&a?-1:1,d=ir(t,e);let{mainAxis:p,crossAxis:h,alignmentAxis:f}="number"==typeof d?{mainAxis:d,crossAxis:0,alignmentAxis:null}:{mainAxis:d.mainAxis||0,crossAxis:d.crossAxis||0,alignmentAxis:d.alignmentAxis};return l&&"number"==typeof f&&(h="end"===l?-1*f:f),a?{x:h*u,y:p*c}:{x:p*c,y:h*u}}(t,e);return r===(null==(n=l.offset)?void 0:n.placement)&&null!=(o=l.arrow)&&o.alignmentOffset?{}:{x:i+a.x,y:s+a.y,data:{...a,placement:r}}}}};function _r(e){var t;return(null==(t=e.ownerDocument)?void 0:t.defaultView)||window}function Cr(e){return _r(e).getComputedStyle(e)}const xr=Math.min,Sr=Math.max,kr=Math.round;function Tr(e){const t=Cr(e);let n=parseFloat(t.width),o=parseFloat(t.height);const i=e.offsetWidth,s=e.offsetHeight,r=kr(n)!==i||kr(o)!==s;return r&&(n=i,o=s),{width:n,height:o,fallback:r}}function $r(e){return Ar(e)?(e.nodeName||"").toLowerCase():""}let Lr;function Pr(){if(Lr)return Lr;const e=navigator.userAgentData;return e&&Array.isArray(e.brands)?(Lr=e.brands.map((e=>e.brand+"/"+e.version)).join(" "),Lr):navigator.userAgent}function Or(e){return e instanceof _r(e).HTMLElement}function Mr(e){return e instanceof _r(e).Element}function Ar(e){return e instanceof _r(e).Node}function Er(e){return"undefined"!=typeof ShadowRoot&&(e instanceof _r(e).ShadowRoot||e instanceof ShadowRoot)}function Dr(e){const{overflow:t,overflowX:n,overflowY:o,display:i}=Cr(e);return/auto|scroll|overlay|hidden|clip/.test(t+o+n)&&!["inline","contents"].includes(i)}function Rr(e){return["table","td","th"].includes($r(e))}function Hr(e){const t=/firefox/i.test(Pr()),n=Cr(e),o=n.backdropFilter||n.WebkitBackdropFilter;return"none"!==n.transform||"none"!==n.perspective||!!o&&"none"!==o||t&&"filter"===n.willChange||t&&!!n.filter&&"none"!==n.filter||["transform","perspective"].some((e=>n.willChange.includes(e)))||["paint","layout","strict","content"].some((e=>{const t=n.contain;return null!=t&&t.includes(e)}))}function jr(){return!/^((?!chrome|android).)*safari/i.test(Pr())}function zr(e){return["html","body","#document"].includes($r(e))}function Br(e){return Mr(e)?e:e.contextElement}const Nr={x:1,y:1};function Fr(e){const t=Br(e);if(!Or(t))return Nr;const n=t.getBoundingClientRect(),{width:o,height:i,fallback:s}=Tr(t);let r=(s?kr(n.width):n.width)/o,l=(s?kr(n.height):n.height)/i;return r&&Number.isFinite(r)||(r=1),l&&Number.isFinite(l)||(l=1),{x:r,y:l}}function Vr(e,t,n,o){var i,s;void 0===t&&(t=!1),void 0===n&&(n=!1);const r=e.getBoundingClientRect(),l=Br(e);let a=Nr;t&&(o?Mr(o)&&(a=Fr(o)):a=Fr(e));const c=l?_r(l):window,u=!jr()&&n;let d=(r.left+(u&&(null==(i=c.visualViewport)?void 0:i.offsetLeft)||0))/a.x,p=(r.top+(u&&(null==(s=c.visualViewport)?void 0:s.offsetTop)||0))/a.y,h=r.width/a.x,f=r.height/a.y;if(l){const e=_r(l),t=o&&Mr(o)?_r(o):o;let n=e.frameElement;for(;n&&o&&t!==e;){const e=Fr(n),t=n.getBoundingClientRect(),o=getComputedStyle(n);t.x+=(n.clientLeft+parseFloat(o.paddingLeft))*e.x,t.y+=(n.clientTop+parseFloat(o.paddingTop))*e.y,d*=e.x,p*=e.y,h*=e.x,f*=e.y,d+=t.x,p+=t.y,n=_r(n).frameElement}}return{width:h,height:f,top:p,right:d+h,bottom:p+f,left:d,x:d,y:p}}function Ir(e){return((Ar(e)?e.ownerDocument:e.document)||window.document).documentElement}function Ur(e){return Mr(e)?{scrollLeft:e.scrollLeft,scrollTop:e.scrollTop}:{scrollLeft:e.pageXOffset,scrollTop:e.pageYOffset}}function Wr(e){return Vr(Ir(e)).left+Ur(e).scrollLeft}function Zr(e){if("html"===$r(e))return e;const t=e.assignedSlot||e.parentNode||Er(e)&&e.host||Ir(e);return Er(t)?t.host:t}function qr(e){const t=Zr(e);return zr(t)?t.ownerDocument.body:Or(t)&&Dr(t)?t:qr(t)}function Gr(e,t){var n;void 0===t&&(t=[]);const o=qr(e),i=o===(null==(n=e.ownerDocument)?void 0:n.body),s=_r(o);return i?t.concat(s,s.visualViewport||[],Dr(o)?o:[]):t.concat(o,Gr(o))}function Yr(e,t,n){return"viewport"===t?gr(function(e,t){const n=_r(e),o=Ir(e),i=n.visualViewport;let s=o.clientWidth,r=o.clientHeight,l=0,a=0;if(i){s=i.width,r=i.height;const e=jr();(e||!e&&"fixed"===t)&&(l=i.offsetLeft,a=i.offsetTop)}return{width:s,height:r,x:l,y:a}}(e,n)):Mr(t)?gr(function(e,t){const n=Vr(e,!0,"fixed"===t),o=n.top+e.clientTop,i=n.left+e.clientLeft,s=Or(e)?Fr(e):{x:1,y:1};return{width:e.clientWidth*s.x,height:e.clientHeight*s.y,x:i*s.x,y:o*s.y}}(t,n)):gr(function(e){const t=Ir(e),n=Ur(e),o=e.ownerDocument.body,i=Sr(t.scrollWidth,t.clientWidth,o.scrollWidth,o.clientWidth),s=Sr(t.scrollHeight,t.clientHeight,o.scrollHeight,o.clientHeight);let r=-n.scrollLeft+Wr(e);const l=-n.scrollTop;return"rtl"===Cr(o).direction&&(r+=Sr(t.clientWidth,o.clientWidth)-i),{width:i,height:s,x:r,y:l}}(Ir(e)))}function Kr(e){return Or(e)&&"fixed"!==Cr(e).position?e.offsetParent:null}function Xr(e){const t=_r(e);let n=Kr(e);for(;n&&Rr(n)&&"static"===Cr(n).position;)n=Kr(n);return n&&("html"===$r(n)||"body"===$r(n)&&"static"===Cr(n).position&&!Hr(n))?t:n||function(e){let t=Zr(e);for(;Or(t)&&!zr(t);){if(Hr(t))return t;t=Zr(t)}return null}(e)||t}function Qr(e,t,n){const o=Or(t),i=Ir(t),s=Vr(e,!0,"fixed"===n,t);let r={scrollLeft:0,scrollTop:0};const l={x:0,y:0};if(o||!o&&"fixed"!==n)if(("body"!==$r(t)||Dr(i))&&(r=Ur(t)),Or(t)){const e=Vr(t,!0);l.x=e.x+t.clientLeft,l.y=e.y+t.clientTop}else i&&(l.x=Wr(i));return{x:s.left+r.scrollLeft-l.x,y:s.top+r.scrollTop-l.y,width:s.width,height:s.height}}const Jr={getClippingRect:function(e){let{element:t,boundary:n,rootBoundary:o,strategy:i}=e;const s=[..."clippingAncestors"===n?function(e,t){const n=t.get(e);if(n)return n;let o=Gr(e).filter((e=>Mr(e)&&"body"!==$r(e))),i=null;const s="fixed"===Cr(e).position;let r=s?Zr(e):e;for(;Mr(r)&&!zr(r);){const e=Cr(r),t=Hr(r);(s?t||i:t||"static"!==e.position||!i||!["absolute","fixed"].includes(i.position))?i=e:o=o.filter((e=>e!==r)),r=Zr(r)}return t.set(e,o),o}(t,this._c):[].concat(n),o],r=s[0],l=s.reduce(((e,n)=>{const o=Yr(t,n,i);return e.top=Sr(o.top,e.top),e.right=xr(o.right,e.right),e.bottom=xr(o.bottom,e.bottom),e.left=Sr(o.left,e.left),e}),Yr(t,r,i));return{width:l.right-l.left,height:l.bottom-l.top,x:l.left,y:l.top}},convertOffsetParentRelativeRectToViewportRelativeRect:function(e){let{rect:t,offsetParent:n,strategy:o}=e;const i=Or(n),s=Ir(n);if(n===s)return t;let r={scrollLeft:0,scrollTop:0},l={x:1,y:1};const a={x:0,y:0};if((i||!i&&"fixed"!==o)&&(("body"!==$r(n)||Dr(s))&&(r=Ur(n)),Or(n))){const e=Vr(n);l=Fr(n),a.x=e.x+n.clientLeft,a.y=e.y+n.clientTop}return{width:t.width*l.x,height:t.height*l.y,x:t.x*l.x-r.scrollLeft*l.x+a.x,y:t.y*l.y-r.scrollTop*l.y+a.y}},isElement:Mr,getDimensions:function(e){return Or(e)?Tr(e):e.getBoundingClientRect()},getOffsetParent:Xr,getDocumentElement:Ir,getScale:Fr,async getElementRects(e){let{reference:t,floating:n,strategy:o}=e;const i=this.getOffsetParent||Xr,s=this.getDimensions;return{reference:Qr(t,await i(n),o),floating:{x:0,y:0,...await s(n)}}},getClientRects:e=>Array.from(e.getClientRects()),isRTL:e=>"rtl"===Cr(e).direction},el=(e,t,n)=>{const o=new Map,i={platform:Jr,...n},s={...i.platform,_c:o};return(async(e,t,n)=>{const{placement:o="bottom",strategy:i="absolute",middleware:s=[],platform:r}=n,l=s.filter(Boolean),a=await(null==r.isRTL?void 0:r.isRTL(t));let c=await r.getElementRects({reference:e,floating:t,strategy:i}),{x:u,y:d}=mr(c,o,a),p=o,h={},f=0;for(let g=0;g<l.length;g++){const{name:n,fn:s}=l[g],{x:m,y:v,data:y,reset:w}=await s({x:u,y:d,initialPlacement:o,placement:p,strategy:i,middlewareData:h,rects:c,platform:r,elements:{reference:e,floating:t}});u=null!=m?m:u,d=null!=v?v:d,h={...h,[n]:{...h[n],...y}},w&&f<=50&&(f++,"object"==typeof w&&(w.placement&&(p=w.placement),w.rects&&(c=!0===w.rects?await r.getElementRects({reference:e,floating:t,strategy:i}):w.rects),({x:u,y:d}=mr(c,p,a))),g=-1)}return{x:u,y:d,placement:p,strategy:i,middlewareData:h}})(e,t,{...i,platform:s})};function tl(e,t){for(const n in t)Object.prototype.hasOwnProperty.call(t,n)&&("object"==typeof t[n]&&e[n]?tl(e[n],t[n]):e[n]=t[n])}const nl={disabled:!1,distance:5,skidding:0,container:"body",boundary:void 0,instantMove:!1,disposeTimeout:150,popperTriggers:[],strategy:"absolute",preventOverflow:!0,flip:!0,shift:!0,overflowPadding:0,arrowPadding:0,arrowOverflow:!0,autoHideOnMousedown:!1,themes:{tooltip:{placement:"top",triggers:["hover","focus","touch"],hideTriggers:e=>[...e,"click"],delay:{show:200,hide:0},handleResize:!1,html:!1,loadingContent:"..."},dropdown:{placement:"bottom",triggers:["click"],delay:0,handleResize:!0,autoHide:!0},menu:{$extend:"dropdown",triggers:["hover","focus"],popperTriggers:["hover"],delay:{show:0,hide:400}}}};function ol(e,t){let n,o=nl.themes[e]||{};do{n=o[t],typeof n>"u"?o.$extend?o=nl.themes[o.$extend]||{}:(o=null,n=nl[t]):o=null}while(o);return n}function il(e){const t=[e];let n=nl.themes[e]||{};do{n.$extend?(t.push(n.$extend),n=nl.themes[n.$extend]||{}):n=null}while(n);return t}let sl=!1;if(typeof window<"u"){sl=!1;try{const e=Object.defineProperty({},"passive",{get(){sl=!0}});window.addEventListener("test",null,e)}catch{}}let rl=!1;typeof window<"u"&&typeof navigator<"u"&&(rl=/iPad|iPhone|iPod/.test(navigator.userAgent)&&!window.MSStream);const ll=["auto","top","bottom","left","right"].reduce(((e,t)=>e.concat([t,`${t}-start`,`${t}-end`])),[]),al={hover:"mouseenter",focus:"focus",click:"click",touch:"touchstart",pointer:"pointerdown"},cl={hover:"mouseleave",focus:"blur",click:"click",touch:"touchend",pointer:"pointerup"};function ul(e,t){const n=e.indexOf(t);-1!==n&&e.splice(n,1)}function dl(){return new Promise((e=>requestAnimationFrame((()=>{requestAnimationFrame(e)}))))}const pl=[];let hl=null;const fl={};function gl(e){let t=fl[e];return t||(t=fl[e]=[]),t}let ml=function(){};function vl(e){return function(t){return ol(t.theme,e)}}typeof window<"u"&&(ml=window.Element);const yl="__floating-vue__popper",wl=()=>en({name:"VPopper",provide(){return{[yl]:{parentPopper:this}}},inject:{[yl]:{default:null}},props:{theme:{type:String,required:!0},targetNodes:{type:Function,required:!0},referenceNode:{type:Function,default:null},popperNode:{type:Function,required:!0},shown:{type:Boolean,default:!1},showGroup:{type:String,default:null},ariaId:{default:null},disabled:{type:Boolean,default:vl("disabled")},positioningDisabled:{type:Boolean,default:vl("positioningDisabled")},placement:{type:String,default:vl("placement"),validator:e=>ll.includes(e)},delay:{type:[String,Number,Object],default:vl("delay")},distance:{type:[Number,String],default:vl("distance")},skidding:{type:[Number,String],default:vl("skidding")},triggers:{type:Array,default:vl("triggers")},showTriggers:{type:[Array,Function],default:vl("showTriggers")},hideTriggers:{type:[Array,Function],default:vl("hideTriggers")},popperTriggers:{type:Array,default:vl("popperTriggers")},popperShowTriggers:{type:[Array,Function],default:vl("popperShowTriggers")},popperHideTriggers:{type:[Array,Function],default:vl("popperHideTriggers")},container:{type:[String,Object,ml,Boolean],default:vl("container")},boundary:{type:[String,ml],default:vl("boundary")},strategy:{type:String,validator:e=>["absolute","fixed"].includes(e),default:vl("strategy")},autoHide:{type:[Boolean,Function],default:vl("autoHide")},handleResize:{type:Boolean,default:vl("handleResize")},instantMove:{type:Boolean,default:vl("instantMove")},eagerMount:{type:Boolean,default:vl("eagerMount")},popperClass:{type:[String,Array,Object],default:vl("popperClass")},computeTransformOrigin:{type:Boolean,default:vl("computeTransformOrigin")},autoMinSize:{type:Boolean,default:vl("autoMinSize")},autoSize:{type:[Boolean,String],default:vl("autoSize")},autoMaxSize:{type:Boolean,default:vl("autoMaxSize")},autoBoundaryMaxSize:{type:Boolean,default:vl("autoBoundaryMaxSize")},preventOverflow:{type:Boolean,default:vl("preventOverflow")},overflowPadding:{type:[Number,String],default:vl("overflowPadding")},arrowPadding:{type:[Number,String],default:vl("arrowPadding")},arrowOverflow:{type:Boolean,default:vl("arrowOverflow")},flip:{type:Boolean,default:vl("flip")},shift:{type:Boolean,default:vl("shift")},shiftCrossAxis:{type:Boolean,default:vl("shiftCrossAxis")},noAutoFocus:{type:Boolean,default:vl("noAutoFocus")},disposeTimeout:{type:Number,default:vl("disposeTimeout")}},emits:{show:()=>!0,hide:()=>!0,"update:shown":e=>!0,"apply-show":()=>!0,"apply-hide":()=>!0,"close-group":()=>!0,"close-directive":()=>!0,"auto-hide":()=>!0,resize:()=>!0},data(){return{isShown:!1,isMounted:!1,skipTransition:!1,classes:{showFrom:!1,showTo:!1,hideFrom:!1,hideTo:!0},result:{x:0,y:0,placement:"",strategy:this.strategy,arrow:{x:0,y:0,centerOffset:0},transformOrigin:null},randomId:`popper_${[Math.random(),Date.now()].map((e=>e.toString(36).substring(2,10))).join("_")}`,shownChildren:new Set,lastAutoHide:!0,pendingHide:!1,containsGlobalTarget:!1,isDisposed:!0,mouseDownContains:!1}},computed:{popperId(){return null!=this.ariaId?this.ariaId:this.randomId},shouldMountContent(){return this.eagerMount||this.isMounted},slotData(){return{popperId:this.popperId,isShown:this.isShown,shouldMountContent:this.shouldMountContent,skipTransition:this.skipTransition,autoHide:"function"==typeof this.autoHide?this.lastAutoHide:this.autoHide,show:this.show,hide:this.hide,handleResize:this.handleResize,onResize:this.onResize,classes:{...this.classes,popperClass:this.popperClass},result:this.positioningDisabled?null:this.result,attrs:this.$attrs}},parentPopper(){var e;return null==(e=this[yl])?void 0:e.parentPopper},hasPopperShowTriggerHover(){var e,t;return(null==(e=this.popperTriggers)?void 0:e.includes("hover"))||(null==(t=this.popperShowTriggers)?void 0:t.includes("hover"))}},watch:{shown:"$_autoShowHide",disabled(e){e?this.dispose():this.init()},async container(){this.isShown&&(this.$_ensureTeleport(),await this.$_computePosition())},triggers:{handler:"$_refreshListeners",deep:!0},positioningDisabled:"$_refreshListeners",...["placement","distance","skidding","boundary","strategy","overflowPadding","arrowPadding","preventOverflow","shift","shiftCrossAxis","flip"].reduce(((e,t)=>(e[t]="$_computePosition",e)),{})},created(){this.autoMinSize&&console.warn('[floating-vue] `autoMinSize` option is deprecated. Use `autoSize="min"` instead.'),this.autoMaxSize&&console.warn("[floating-vue] `autoMaxSize` option is deprecated. Use `autoBoundaryMaxSize` instead.")},mounted(){this.init(),this.$_detachPopperNode()},activated(){this.$_autoShowHide()},deactivated(){this.hide()},beforeUnmount(){this.dispose()},methods:{show({event:e=null,skipDelay:t=!1,force:n=!1}={}){var o,i;null!=(o=this.parentPopper)&&o.lockedChild&&this.parentPopper.lockedChild!==this||(this.pendingHide=!1,(n||!this.disabled)&&((null==(i=this.parentPopper)?void 0:i.lockedChild)===this&&(this.parentPopper.lockedChild=null),this.$_scheduleShow(e,t),this.$emit("show"),this.$_showFrameLocked=!0,requestAnimationFrame((()=>{this.$_showFrameLocked=!1}))),this.$emit("update:shown",!0))},hide({event:e=null,skipDelay:t=!1}={}){var n;if(!this.$_hideInProgress){if(this.shownChildren.size>0)return void(this.pendingHide=!0);if(this.hasPopperShowTriggerHover&&this.$_isAimingPopper())return void(this.parentPopper&&(this.parentPopper.lockedChild=this,clearTimeout(this.parentPopper.lockedChildTimer),this.parentPopper.lockedChildTimer=setTimeout((()=>{this.parentPopper.lockedChild===this&&(this.parentPopper.lockedChild.hide({skipDelay:t}),this.parentPopper.lockedChild=null)}),1e3)));(null==(n=this.parentPopper)?void 0:n.lockedChild)===this&&(this.parentPopper.lockedChild=null),this.pendingHide=!1,this.$_scheduleHide(e,t),this.$emit("hide"),this.$emit("update:shown",!1)}},init(){var e;this.isDisposed&&(this.isDisposed=!1,this.isMounted=!1,this.$_events=[],this.$_preventShow=!1,this.$_referenceNode=(null==(e=this.referenceNode)?void 0:e.call(this))??this.$el,this.$_targetNodes=this.targetNodes().filter((e=>e.nodeType===e.ELEMENT_NODE)),this.$_popperNode=this.popperNode(),this.$_innerNode=this.$_popperNode.querySelector(".v-popper__inner"),this.$_arrowNode=this.$_popperNode.querySelector(".v-popper__arrow-container"),this.$_swapTargetAttrs("title","data-original-title"),this.$_detachPopperNode(),this.triggers.length&&this.$_addEventListeners(),this.shown&&this.show())},dispose(){this.isDisposed||(this.isDisposed=!0,this.$_removeEventListeners(),this.hide({skipDelay:!0}),this.$_detachPopperNode(),this.isMounted=!1,this.isShown=!1,this.$_updateParentShownChildren(!1),this.$_swapTargetAttrs("data-original-title","title"))},async onResize(){this.isShown&&(await this.$_computePosition(),this.$emit("resize"))},async $_computePosition(){if(this.isDisposed||this.positioningDisabled)return;const e={strategy:this.strategy,middleware:[]};(this.distance||this.skidding)&&e.middleware.push(br({mainAxis:this.distance,crossAxis:this.skidding}));const t=this.placement.startsWith("auto");if(t?e.middleware.push(yr({alignment:this.placement.split("-")[1]??""})):e.placement=this.placement,this.preventOverflow&&(this.shift&&e.middleware.push((void 0===(n={padding:this.overflowPadding,boundary:this.boundary,crossAxis:this.shiftCrossAxis})&&(n={}),{name:"shift",options:n,async fn(e){const{x:t,y:o,placement:i}=e,{mainAxis:s=!0,crossAxis:r=!1,limiter:l={fn:e=>{let{x:t,y:n}=e;return{x:t,y:n}}},...a}=ir(n,e),c={x:t,y:o},u=await vr(e,a),d=cr(sr(i)),p=lr(d);let h=c[p],f=c[d];if(s){const e="y"===p?"bottom":"right";h=or(h+u["y"===p?"top":"left"],h,h-u[e])}if(r){const e="y"===d?"bottom":"right";f=or(f+u["y"===d?"top":"left"],f,f-u[e])}const g=l.fn({...e,[p]:h,[d]:f});return{...g,data:{x:g.x-t,y:g.y-o,enabled:{[p]:s,[d]:r}}}}})),!t&&this.flip&&e.middleware.push(wr({padding:this.overflowPadding,boundary:this.boundary}))),e.middleware.push((e=>({name:"arrow",options:e,async fn(t){const{x:n,y:o,placement:i,rects:s,platform:r,elements:l,middlewareData:a}=t,{element:c,padding:u=0}=ir(e,t)||{};if(null==c)return{};const d=fr(u),p={x:n,y:o},h=ur(i),f=ar(h),g=await r.getDimensions(c),m="y"===h,v=m?"top":"left",y=m?"bottom":"right",w=m?"clientHeight":"clientWidth",b=s.reference[f]+s.reference[h]-p[h]-s.floating[f],C=p[h]-s.reference[h],x=await(null==r.getOffsetParent?void 0:r.getOffsetParent(c));let S=x?x[w]:0;S&&await(null==r.isElement?void 0:r.isElement(x))||(S=l.floating[w]||s.floating[f]);const k=b/2-C/2,T=S/2-g[f]/2-1,L=Js(d[v],T),P=Js(d[y],T),O=L,M=S-g[f]-P,A=S/2-g[f]/2+k,E=or(O,A,M),D=!a.arrow&&null!=rr(i)&&A!==E&&s.reference[f]/2-(A<O?L:P)-g[f]/2<0,R=D?A<O?A-O:A-M:0;return{[h]:p[h]+R,data:{[h]:E,centerOffset:A-E-R,...D&&{alignmentOffset:R}},reset:D}}}))({element:this.$_arrowNode,padding:this.arrowPadding})),this.arrowOverflow&&e.middleware.push({name:"arrowOverflow",fn:({placement:e,rects:t,middlewareData:n})=>{let o;const{centerOffset:i}=n.arrow;return o=e.startsWith("top")||e.startsWith("bottom")?Math.abs(i)>t.reference.width/2:Math.abs(i)>t.reference.height/2,{data:{overflow:o}}}}),this.autoMinSize||this.autoSize){const t=this.autoSize?this.autoSize:this.autoMinSize?"min":null;e.middleware.push({name:"autoSize",fn:({rects:e,placement:n,middlewareData:o})=>{var i;if(null!=(i=o.autoSize)&&i.skip)return{};let s,r;return n.startsWith("top")||n.startsWith("bottom")?s=e.reference.width:r=e.reference.height,this.$_innerNode.style["min"===t?"minWidth":"max"===t?"maxWidth":"width"]=null!=s?`${s}px`:null,this.$_innerNode.style["min"===t?"minHeight":"max"===t?"maxHeight":"height"]=null!=r?`${r}px`:null,{data:{skip:!0},reset:{rects:!0}}}})}var n;(this.autoMaxSize||this.autoBoundaryMaxSize)&&(this.$_innerNode.style.maxWidth=null,this.$_innerNode.style.maxHeight=null,e.middleware.push(function(e){return void 0===e&&(e={}),{name:"size",options:e,async fn(t){var n,o;const{placement:i,rects:s,platform:r,elements:l}=t,{apply:a=()=>{},...c}=ir(e,t),u=await vr(t,c),d=sr(i),p=rr(i),h="y"===cr(i),{width:f,height:g}=s.floating;let m,v;"top"===d||"bottom"===d?(m=d,v=p===(await(null==r.isRTL?void 0:r.isRTL(l.floating))?"start":"end")?"left":"right"):(v=d,m="end"===p?"top":"bottom");const y=g-u.top-u.bottom,w=f-u.left-u.right,b=Js(g-u[m],y),C=Js(f-u[v],w),x=!t.middlewareData.shift;let S=b,k=C;if(null!=(n=t.middlewareData.shift)&&n.enabled.x&&(k=w),null!=(o=t.middlewareData.shift)&&o.enabled.y&&(S=y),x&&!p){const e=er(u.left,0),t=er(u.right,0),n=er(u.top,0),o=er(u.bottom,0);h?k=f-2*(0!==e||0!==t?e+t:er(u.left,u.right)):S=g-2*(0!==n||0!==o?n+o:er(u.top,u.bottom))}await a({...t,availableWidth:k,availableHeight:S});const T=await r.getDimensions(l.floating);return f!==T.width||g!==T.height?{reset:{rects:!0}}:{}}}}({boundary:this.boundary,padding:this.overflowPadding,apply:({availableWidth:e,availableHeight:t})=>{this.$_innerNode.style.maxWidth=null!=e?`${e}px`:null,this.$_innerNode.style.maxHeight=null!=t?`${t}px`:null}})));const o=await el(this.$_referenceNode,this.$_popperNode,e);Object.assign(this.result,{x:o.x,y:o.y,placement:o.placement,strategy:o.strategy,arrow:{...o.middlewareData.arrow,...o.middlewareData.arrowOverflow}})},$_scheduleShow(e,t=!1){if(this.$_updateParentShownChildren(!0),this.$_hideInProgress=!1,clearTimeout(this.$_scheduleTimer),hl&&this.instantMove&&hl.instantMove&&hl!==this.parentPopper)return hl.$_applyHide(!0),void this.$_applyShow(!0);t?this.$_applyShow():this.$_scheduleTimer=setTimeout(this.$_applyShow.bind(this),this.$_computeDelay("show"))},$_scheduleHide(e,t=!1){this.shownChildren.size>0?this.pendingHide=!0:(this.$_updateParentShownChildren(!1),this.$_hideInProgress=!0,clearTimeout(this.$_scheduleTimer),this.isShown&&(hl=this),t?this.$_applyHide():this.$_scheduleTimer=setTimeout(this.$_applyHide.bind(this),this.$_computeDelay("hide")))},$_computeDelay(e){const t=this.delay;return parseInt(t&&t[e]||t||0)},async $_applyShow(e=!1){clearTimeout(this.$_disposeTimer),clearTimeout(this.$_scheduleTimer),this.skipTransition=e,!this.isShown&&(this.$_ensureTeleport(),await dl(),await this.$_computePosition(),await this.$_applyShowEffect(),this.positioningDisabled||this.$_registerEventListeners([...Gr(this.$_referenceNode),...Gr(this.$_popperNode)],"scroll",(()=>{this.$_computePosition()})))},async $_applyShowEffect(){if(this.$_hideInProgress)return;if(this.computeTransformOrigin){const e=this.$_referenceNode.getBoundingClientRect(),t=this.$_popperNode.querySelector(".v-popper__wrapper"),n=t.parentNode.getBoundingClientRect(),o=e.x+e.width/2-(n.left+t.offsetLeft),i=e.y+e.height/2-(n.top+t.offsetTop);this.result.transformOrigin=`${o}px ${i}px`}this.isShown=!0,this.$_applyAttrsToTarget({"aria-describedby":this.popperId,"data-popper-shown":""});const e=this.showGroup;if(e){let t;for(let n=0;n<pl.length;n++)t=pl[n],t.showGroup!==e&&(t.hide(),t.$emit("close-group"))}pl.push(this),document.body.classList.add("v-popper--some-open");for(const t of il(this.theme))gl(t).push(this),document.body.classList.add(`v-popper--some-open--${t}`);this.$emit("apply-show"),this.classes.showFrom=!0,this.classes.showTo=!1,this.classes.hideFrom=!1,this.classes.hideTo=!1,await dl(),this.classes.showFrom=!1,this.classes.showTo=!0,this.noAutoFocus||this.$_popperNode.focus()},async $_applyHide(e=!1){if(this.shownChildren.size>0)return this.pendingHide=!0,void(this.$_hideInProgress=!1);if(clearTimeout(this.$_scheduleTimer),!this.isShown)return;this.skipTransition=e,ul(pl,this),0===pl.length&&document.body.classList.remove("v-popper--some-open");for(const n of il(this.theme)){const e=gl(n);ul(e,this),0===e.length&&document.body.classList.remove(`v-popper--some-open--${n}`)}hl===this&&(hl=null),this.isShown=!1,this.$_applyAttrsToTarget({"aria-describedby":void 0,"data-popper-shown":void 0}),clearTimeout(this.$_disposeTimer);const t=this.disposeTimeout;null!==t&&(this.$_disposeTimer=setTimeout((()=>{this.$_popperNode&&(this.$_detachPopperNode(),this.isMounted=!1)}),t)),this.$_removeEventListeners("scroll"),this.$emit("apply-hide"),this.classes.showFrom=!1,this.classes.showTo=!1,this.classes.hideFrom=!0,this.classes.hideTo=!1,await dl(),this.classes.hideFrom=!1,this.classes.hideTo=!0},$_autoShowHide(){this.shown?this.show():this.hide()},$_ensureTeleport(){if(this.isDisposed)return;let e=this.container;if("string"==typeof e?e=window.document.querySelector(e):!1===e&&(e=this.$_targetNodes[0].parentNode),!e)throw new Error("No container for popover: "+this.container);e.appendChild(this.$_popperNode),this.isMounted=!0},$_addEventListeners(){const e=e=>{this.isShown&&!this.$_hideInProgress||(e.usedByTooltip=!0,!this.$_preventShow&&this.show({event:e}))};this.$_registerTriggerListeners(this.$_targetNodes,al,this.triggers,this.showTriggers,e),this.$_registerTriggerListeners([this.$_popperNode],al,this.popperTriggers,this.popperShowTriggers,e);const t=e=>{e.usedByTooltip||this.hide({event:e})};this.$_registerTriggerListeners(this.$_targetNodes,cl,this.triggers,this.hideTriggers,t),this.$_registerTriggerListeners([this.$_popperNode],cl,this.popperTriggers,this.popperHideTriggers,t)},$_registerEventListeners(e,t,n){this.$_events.push({targetNodes:e,eventType:t,handler:n}),e.forEach((e=>e.addEventListener(t,n,sl?{passive:!0}:void 0)))},$_registerTriggerListeners(e,t,n,o,i){let s=n;null!=o&&(s="function"==typeof o?o(s):o),s.forEach((n=>{const o=t[n];o&&this.$_registerEventListeners(e,o,i)}))},$_removeEventListeners(e){const t=[];this.$_events.forEach((n=>{const{targetNodes:o,eventType:i,handler:s}=n;e&&e!==i?t.push(n):o.forEach((e=>e.removeEventListener(i,s)))})),this.$_events=t},$_refreshListeners(){this.isDisposed||(this.$_removeEventListeners(),this.$_addEventListeners())},$_handleGlobalClose(e,t=!1){this.$_showFrameLocked||(this.hide({event:e}),e.closePopover?this.$emit("close-directive"):this.$emit("auto-hide"),t&&(this.$_preventShow=!0,setTimeout((()=>{this.$_preventShow=!1}),300)))},$_detachPopperNode(){this.$_popperNode.parentNode&&this.$_popperNode.parentNode.removeChild(this.$_popperNode)},$_swapTargetAttrs(e,t){for(const n of this.$_targetNodes){const o=n.getAttribute(e);o&&(n.removeAttribute(e),n.setAttribute(t,o))}},$_applyAttrsToTarget(e){for(const t of this.$_targetNodes)for(const n in e){const o=e[n];null==o?t.removeAttribute(n):t.setAttribute(n,o)}},$_updateParentShownChildren(e){let t=this.parentPopper;for(;t;)e?t.shownChildren.add(this.randomId):(t.shownChildren.delete(this.randomId),t.pendingHide&&t.hide()),t=t.parentPopper},$_isAimingPopper(){const e=this.$_referenceNode.getBoundingClientRect();if(Tl>=e.left&&Tl<=e.right&&_>=e.top&&_<=e.bottom){const e=this.$_popperNode.getBoundingClientRect(),t=Tl-Sl,n=_-kl,o=e.left+e.width/2-Sl+(e.top+e.height/2)-kl+e.width+e.height,i=Sl+t*o,s=kl+n*o;return $l(Sl,kl,i,s,e.left,e.top,e.left,e.bottom)||$l(Sl,kl,i,s,e.left,e.top,e.right,e.top)||$l(Sl,kl,i,s,e.right,e.top,e.right,e.bottom)||$l(Sl,kl,i,s,e.left,e.bottom,e.right,e.bottom)}return!1}},render(){return this.$slots.default(this.slotData)}});if(typeof document<"u"&&typeof window<"u"){if(rl){const e=!sl||{passive:!0,capture:!0};document.addEventListener("touchstart",(e=>bl(e,!0)),e),document.addEventListener("touchend",(e=>_l(e,!0)),e)}else window.addEventListener("mousedown",(e=>bl(e,!1)),!0),window.addEventListener("click",(e=>_l(e,!1)),!0);window.addEventListener("resize",(function(){for(let e=0;e<pl.length;e++)pl[e].$_computePosition()}))}function bl(e,t){if(nl.autoHideOnMousedown)Cl(e,t);else for(let n=0;n<pl.length;n++){const t=pl[n];try{t.mouseDownContains=t.popperNode().contains(e.target)}catch{}}}function _l(e,t){nl.autoHideOnMousedown||Cl(e,t)}function Cl(e,t){const n={};for(let o=pl.length-1;o>=0;o--){const i=pl[o];try{const o=i.containsGlobalTarget=i.mouseDownContains||i.popperNode().contains(e.target);i.pendingHide=!1,requestAnimationFrame((()=>{if(i.pendingHide=!1,!n[i.randomId]&&xl(i,o,e)){if(i.$_handleGlobalClose(e,t),!e.closeAllPopover&&e.closePopover&&o){let e=i.parentPopper;for(;e;)n[e.randomId]=!0,e=e.parentPopper;return}let s=i.parentPopper;for(;s&&xl(s,s.containsGlobalTarget,e);)s.$_handleGlobalClose(e,t),s=s.parentPopper}}))}catch{}}}function xl(e,t,n){return n.closeAllPopover||n.closePopover&&t||function(e,t){if("function"==typeof e.autoHide){const n=e.autoHide(t);return e.lastAutoHide=n,n}return e.autoHide}(e,n)&&!t}let Sl=0,kl=0,Tl=0,_=0;function $l(e,t,n,o,i,s,r,l){const a=((r-i)*(t-s)-(l-s)*(e-i))/((l-s)*(n-e)-(r-i)*(o-t)),c=((n-e)*(t-s)-(o-t)*(e-i))/((l-s)*(n-e)-(r-i)*(o-t));return a>=0&&a<=1&&c>=0&&c<=1}typeof window<"u"&&window.addEventListener("mousemove",(e=>{Sl=Tl,kl=_,Tl=e.clientX,_=e.clientY}),sl?{passive:!0}:void 0);const Ll=(e,t)=>{const n=e.__vccOpts||e;for(const[o,i]of t)n[o]=i;return n};const Pl=Ll({extends:wl()},[["render",function(e,t,n,o,i,s){return Io(),qo("div",{ref:"reference",class:U(["v-popper",{"v-popper--shown":e.slotData.isShown}])},[Pn(e.$slots,"default",W(ti(e.slotData)))],2)}]]);let Ol;function Ml(){Ml.init||(Ml.init=!0,Ol=-1!==function(){var e=window.navigator.userAgent,t=e.indexOf("MSIE ");if(t>0)return parseInt(e.substring(t+5,e.indexOf(".",t)),10);if(e.indexOf("Trident/")>0){var n=e.indexOf("rv:");return parseInt(e.substring(n+3,e.indexOf(".",n)),10)}var o=e.indexOf("Edge/");return o>0?parseInt(e.substring(o+5,e.indexOf(".",o)),10):-1}())}var Al={name:"ResizeObserver",props:{emitOnMount:{type:Boolean,default:!1},ignoreWidth:{type:Boolean,default:!1},ignoreHeight:{type:Boolean,default:!1}},emits:["notify"],mounted(){Ml(),zt((()=>{this._w=this.$el.offsetWidth,this._h=this.$el.offsetHeight,this.emitOnMount&&this.emitSize()}));const e=document.createElement("object");this._resizeObject=e,e.setAttribute("aria-hidden","true"),e.setAttribute("tabindex",-1),e.onload=this.addResizeHandlers,e.type="text/html",Ol&&this.$el.appendChild(e),e.data="about:blank",Ol||this.$el.appendChild(e)},beforeUnmount(){this.removeResizeHandlers()},methods:{compareAndNotify(){(!this.ignoreWidth&&this._w!==this.$el.offsetWidth||!this.ignoreHeight&&this._h!==this.$el.offsetHeight)&&(this._w=this.$el.offsetWidth,this._h=this.$el.offsetHeight,this.emitSize())},emitSize(){this.$emit("notify",{width:this._w,height:this._h})},addResizeHandlers(){this._resizeObject.contentDocument.defaultView.addEventListener("resize",this.compareAndNotify),this.compareAndNotify()},removeResizeHandlers(){this._resizeObject&&this._resizeObject.onload&&(!Ol&&this._resizeObject.contentDocument&&this._resizeObject.contentDocument.defaultView.removeEventListener("resize",this.compareAndNotify),this.$el.removeChild(this._resizeObject),this._resizeObject.onload=null,this._resizeObject=null)}}};Zt="data-v-b329ee4c";const El={class:"resize-observer",tabindex:"-1"};Zt=null;const Dl=Gt()(((e,t,n,o,i,s)=>(Io(),Go("div",El))));Al.render=Dl,Al.__scopeId="data-v-b329ee4c",Al.__file="src/components/ResizeObserver.vue";const Rl=(e="theme")=>({computed:{themeClass(){return function(e){const t=[e];let n=nl.themes[e]||{};do{n.$extend&&!n.$resetCss?(t.push(n.$extend),n=nl.themes[n.$extend]||{}):n=null}while(n);return t.map((e=>`v-popper--theme-${e}`))}(this[e])}}}),Hl=en({name:"VPopperContent",components:{ResizeObserver:Al},mixins:[Rl()],props:{popperId:String,theme:String,shown:Boolean,mounted:Boolean,skipTransition:Boolean,autoHide:Boolean,handleResize:Boolean,classes:Object,result:Object},emits:["hide","resize"],methods:{toPx:e=>null==e||isNaN(e)?null:`${e}px`}}),jl=["id","aria-hidden","tabindex","data-popper-placement"],zl={ref:"inner",class:"v-popper__inner"},Bl=[Jo("div",{class:"v-popper__arrow-outer"},null,-1),Jo("div",{class:"v-popper__arrow-inner"},null,-1)];const Nl=Ll(Hl,[["render",function(e,t,n,o,i,s){const r=xn("ResizeObserver");return Io(),qo("div",{id:e.popperId,ref:"popover",class:U(["v-popper__popper",[e.themeClass,e.classes.popperClass,{"v-popper__popper--shown":e.shown,"v-popper__popper--hidden":!e.shown,"v-popper__popper--show-from":e.classes.showFrom,"v-popper__popper--show-to":e.classes.showTo,"v-popper__popper--hide-from":e.classes.hideFrom,"v-popper__popper--hide-to":e.classes.hideTo,"v-popper__popper--skip-transition":e.skipTransition,"v-popper__popper--arrow-overflow":e.result&&e.result.arrow.overflow,"v-popper__popper--no-positioning":!e.result}]]),style:B(e.result?{position:e.result.strategy,transform:`translate3d(${Math.round(e.result.x)}px,${Math.round(e.result.y)}px,0)`}:void 0),"aria-hidden":e.shown?"false":"true",tabindex:e.autoHide?0:void 0,"data-popper-placement":e.result?e.result.placement:void 0,onKeyup:t[2]||(t[2]=as((t=>e.autoHide&&e.$emit("hide")),["esc"]))},[Jo("div",{class:"v-popper__backdrop",onClick:t[0]||(t[0]=t=>e.autoHide&&e.$emit("hide"))}),Jo("div",{class:"v-popper__wrapper",style:B(e.result?{transformOrigin:e.result.transformOrigin}:void 0)},[Jo("div",zl,[e.mounted?(Io(),qo(jo,{key:0},[Jo("div",null,[Pn(e.$slots,"default")]),e.handleResize?(Io(),Go(r,{key:0,onNotify:t[1]||(t[1]=t=>e.$emit("resize",t))})):si("",!0)],64)):si("",!0)],512),Jo("div",{ref:"arrow",class:"v-popper__arrow-container",style:B(e.result?{left:e.toPx(e.result.arrow.x),top:e.toPx(e.result.arrow.y)}:void 0)},Bl,4)],4)],46,jl)}]]),Fl={methods:{show(...e){return this.$refs.popper.show(...e)},hide(...e){return this.$refs.popper.hide(...e)},dispose(...e){return this.$refs.popper.dispose(...e)},onResize(...e){return this.$refs.popper.onResize(...e)}}};let Vl=function(){};typeof window<"u"&&(Vl=window.Element);const Il=Ll(en({name:"VPopperWrapper",components:{Popper:Pl,PopperContent:Nl},mixins:[Fl,Rl("finalTheme")],props:{theme:{type:String,default:null},referenceNode:{type:Function,default:null},shown:{type:Boolean,default:!1},showGroup:{type:String,default:null},ariaId:{default:null},disabled:{type:Boolean,default:void 0},positioningDisabled:{type:Boolean,default:void 0},placement:{type:String,default:void 0},delay:{type:[String,Number,Object],default:void 0},distance:{type:[Number,String],default:void 0},skidding:{type:[Number,String],default:void 0},triggers:{type:Array,default:void 0},showTriggers:{type:[Array,Function],default:void 0},hideTriggers:{type:[Array,Function],default:void 0},popperTriggers:{type:Array,default:void 0},popperShowTriggers:{type:[Array,Function],default:void 0},popperHideTriggers:{type:[Array,Function],default:void 0},container:{type:[String,Object,Vl,Boolean],default:void 0},boundary:{type:[String,Vl],default:void 0},strategy:{type:String,default:void 0},autoHide:{type:[Boolean,Function],default:void 0},handleResize:{type:Boolean,default:void 0},instantMove:{type:Boolean,default:void 0},eagerMount:{type:Boolean,default:void 0},popperClass:{type:[String,Array,Object],default:void 0},computeTransformOrigin:{type:Boolean,default:void 0},autoMinSize:{type:Boolean,default:void 0},autoSize:{type:[Boolean,String],default:void 0},autoMaxSize:{type:Boolean,default:void 0},autoBoundaryMaxSize:{type:Boolean,default:void 0},preventOverflow:{type:Boolean,default:void 0},overflowPadding:{type:[Number,String],default:void 0},arrowPadding:{type:[Number,String],default:void 0},arrowOverflow:{type:Boolean,default:void 0},flip:{type:Boolean,default:void 0},shift:{type:Boolean,default:void 0},shiftCrossAxis:{type:Boolean,default:void 0},noAutoFocus:{type:Boolean,default:void 0},disposeTimeout:{type:Number,default:void 0}},emits:{show:()=>!0,hide:()=>!0,"update:shown":e=>!0,"apply-show":()=>!0,"apply-hide":()=>!0,"close-group":()=>!0,"close-directive":()=>!0,"auto-hide":()=>!0,resize:()=>!0},computed:{finalTheme(){return this.theme??this.$options.vPopperTheme}},methods:{getTargetNodes(){return Array.from(this.$el.children).filter((e=>e!==this.$refs.popperContent.$el))}}}),[["render",function(e,t,n,o,i,s){const r=xn("PopperContent"),l=xn("Popper");return Io(),Go(l,ci({ref:"popper"},e.$props,{theme:e.finalTheme,"target-nodes":e.getTargetNodes,"popper-node":()=>e.$refs.popperContent.$el,class:[e.themeClass],onShow:t[0]||(t[0]=()=>e.$emit("show")),onHide:t[1]||(t[1]=()=>e.$emit("hide")),"onUpdate:shown":t[2]||(t[2]=t=>e.$emit("update:shown",t)),onApplyShow:t[3]||(t[3]=()=>e.$emit("apply-show")),onApplyHide:t[4]||(t[4]=()=>e.$emit("apply-hide")),onCloseGroup:t[5]||(t[5]=()=>e.$emit("close-group")),onCloseDirective:t[6]||(t[6]=()=>e.$emit("close-directive")),onAutoHide:t[7]||(t[7]=()=>e.$emit("auto-hide")),onResize:t[8]||(t[8]=()=>e.$emit("resize"))}),{default:Yt((({popperId:t,isShown:n,shouldMountContent:o,skipTransition:i,autoHide:s,show:l,hide:a,handleResize:c,onResize:u,classes:d,result:p})=>[Pn(e.$slots,"default",{shown:n,show:l,hide:a}),ei(r,{ref:"popperContent","popper-id":t,theme:e.finalTheme,shown:n,mounted:o,"skip-transition":i,"auto-hide":s,"handle-resize":c,classes:d,result:p,onHide:a,onResize:u},{default:Yt((()=>[Pn(e.$slots,"popper",{shown:n,hide:a})])),_:2},1032,["popper-id","theme","shown","mounted","skip-transition","auto-hide","handle-resize","classes","result","onHide","onResize"])])),_:3},16,["theme","target-nodes","popper-node","class"])}]]),Ul={...Il,name:"VDropdown",vPopperTheme:"dropdown"},Wl={...Il,name:"VMenu",vPopperTheme:"menu"},Zl={...Il,name:"VTooltip",vPopperTheme:"tooltip"},ql=en({name:"VTooltipDirective",components:{Popper:wl(),PopperContent:Nl},mixins:[Fl],inheritAttrs:!1,props:{theme:{type:String,default:"tooltip"},html:{type:Boolean,default:e=>ol(e.theme,"html")},content:{type:[String,Number,Function],default:null},loadingContent:{type:String,default:e=>ol(e.theme,"loadingContent")},targetNodes:{type:Function,required:!0}},data:()=>({asyncContent:null}),computed:{isContentAsync(){return"function"==typeof this.content},loading(){return this.isContentAsync&&null==this.asyncContent},finalContent(){return this.isContentAsync?this.loading?this.loadingContent:this.asyncContent:this.content}},watch:{content:{handler(){this.fetchContent(!0)},immediate:!0},async finalContent(){await this.$nextTick(),this.$refs.popper.onResize()}},created(){this.$_fetchId=0},methods:{fetchContent(e){if("function"==typeof this.content&&this.$_isShown&&(e||!this.$_loading&&null==this.asyncContent)){this.asyncContent=null,this.$_loading=!0;const e=++this.$_fetchId,t=this.content(this);t.then?t.then((t=>this.onResult(e,t))):this.onResult(e,t)}},onResult(e,t){e===this.$_fetchId&&(this.$_loading=!1,this.asyncContent=t)},onShow(){this.$_isShown=!0,this.fetchContent()},onHide(){this.$_isShown=!1}}}),Gl=["innerHTML"],Yl=["textContent"];const Kl=Ll(ql,[["render",function(e,t,n,o,i,s){const r=xn("PopperContent"),l=xn("Popper");return Io(),Go(l,ci({ref:"popper"},e.$attrs,{theme:e.theme,"target-nodes":e.targetNodes,"popper-node":()=>e.$refs.popperContent.$el,onApplyShow:e.onShow,onApplyHide:e.onHide}),{default:Yt((({popperId:t,isShown:n,shouldMountContent:o,skipTransition:i,autoHide:s,hide:l,handleResize:a,onResize:c,classes:u,result:d})=>[ei(r,{ref:"popperContent",class:U({"v-popper--tooltip-loading":e.loading}),"popper-id":t,theme:e.theme,shown:n,mounted:o,"skip-transition":i,"auto-hide":s,"handle-resize":a,classes:u,result:d,onHide:l,onResize:c},{default:Yt((()=>[e.html?(Io(),qo("div",{key:0,innerHTML:e.finalContent},null,8,Gl)):(Io(),qo("div",{key:1,textContent:Y(e.finalContent)},null,8,Yl))])),_:2},1032,["class","popper-id","theme","shown","mounted","skip-transition","auto-hide","handle-resize","classes","result","onHide","onResize"])])),_:1},16,["theme","target-nodes","popper-node","onApplyShow","onApplyHide"])}]]),Xl="v-popper--has-tooltip";function Ql(e,t,n){let o;const i=typeof t;return o="string"===i?{content:t}:t&&"object"===i?t:{content:!1},o.placement=function(e,t){let n=e.placement;if(!n&&t)for(const o of ll)t[o]&&(n=o);return n||(n=ol(e.theme||"tooltip","placement")),n}(o,n),o.targetNodes=()=>[e],o.referenceNode=()=>e,o}let Jl,ea,ta=0;function na(){if(Jl)return;ea=yt([]),Jl=ds({name:"VTooltipDirectiveApp",setup:()=>({directives:ea}),render(){return this.directives.map((e=>function(e,t,n){const o=arguments.length;return 2===o?v(t)&&!d(t)?Yo(t)?ei(e,null,[t]):ei(e,t):ei(e,null,t):(o>3?n=Array.prototype.slice.call(arguments,2):3===o&&Yo(n)&&(n=[n]),ei(e,t,n))}(Kl,{...e.options,shown:e.shown||e.options.shown,key:e.id})))},devtools:{hide:!0}});const e=document.createElement("div");document.body.appendChild(e),Jl.mount(e)}function oa(e){if(e.$_popper){const t=ea.value.indexOf(e.$_popper.item);-1!==t&&ea.value.splice(t,1),delete e.$_popper,delete e.$_popperOldShown,delete e.$_popperMountTarget}e.classList&&e.classList.remove(Xl)}function ia(e,{value:t,modifiers:n}){const o=Ql(e,t,n);if(!o.content||ol(o.theme||"tooltip","disabled"))oa(e);else{let i;e.$_popper?(i=e.$_popper,i.options.value=o):i=function(e,t,n){na();const o=yt(Ql(e,t,n)),i=yt(!1),s={id:ta++,options:o,shown:i};return ea.value.push(s),e.classList&&e.classList.add(Xl),e.$_popper={options:o,item:s,show(){i.value=!0},hide(){i.value=!1}}}(e,t,n),typeof t.shown<"u"&&t.shown!==e.$_popperOldShown&&(e.$_popperOldShown=t.shown,t.shown?i.show():i.hide())}}const sa={beforeMount:ia,updated:ia,beforeUnmount(e){oa(e)}};function ra(e){e.addEventListener("mousedown",aa),e.addEventListener("click",aa),e.addEventListener("touchstart",ca,!!sl&&{passive:!0})}function la(e){e.removeEventListener("mousedown",aa),e.removeEventListener("click",aa),e.removeEventListener("touchstart",ca),e.removeEventListener("touchend",ua),e.removeEventListener("touchcancel",da)}function aa(e){const t=e.currentTarget;e.closePopover=!t.$_vclosepopover_touch,e.closeAllPopover=t.$_closePopoverModifiers&&!!t.$_closePopoverModifiers.all}function ca(e){if(1===e.changedTouches.length){const t=e.currentTarget;t.$_vclosepopover_touch=!0;const n=e.changedTouches[0];t.$_vclosepopover_touchPoint=n,t.addEventListener("touchend",ua),t.addEventListener("touchcancel",da)}}function ua(e){const t=e.currentTarget;if(t.$_vclosepopover_touch=!1,1===e.changedTouches.length){const n=e.changedTouches[0],o=t.$_vclosepopover_touchPoint;e.closePopover=Math.abs(n.screenY-o.screenY)<20&&Math.abs(n.screenX-o.screenX)<20,e.closeAllPopover=t.$_closePopoverModifiers&&!!t.$_closePopoverModifiers.all}}function da(e){e.currentTarget.$_vclosepopover_touch=!1}const pa={beforeMount(e,{value:t,modifiers:n}){e.$_closePopoverModifiers=n,(typeof t>"u"||t)&&ra(e)},updated(e,{value:t,oldValue:n,modifiers:o}){e.$_closePopoverModifiers=o,t!==n&&(typeof t>"u"||t?ra(e):la(e))},beforeUnmount(e){la(e)}};const ha={version:"5.2.2",install:function(e,t={}){e.$_vTooltipInstalled||(e.$_vTooltipInstalled=!0,tl(nl,t),e.directive("tooltip",sa),e.directive("close-popper",pa),e.component("VTooltip",Zl),e.component("VDropdown",Ul),e.component("VMenu",Wl))},options:nl};var fa,ga,ma,va,ya=Object.create,wa=Object.defineProperty,ba=Object.getOwnPropertyDescriptor,_a=(e,t)=>(t=Symbol[e])?t:Symbol.for("Symbol."+e),Ca=e=>{throw TypeError(e)},xa=(e,t)=>wa(e,"name",{value:t,configurable:!0}),Sa=["class","method","getter","setter","accessor","field","value","get","set"],ka=e=>void 0!==e&&"function"!=typeof e?Ca("Function expected"):e,Ta=(e,t,n,o,i)=>({kind:Sa[e],name:t,metadata:o,addInitializer:e=>n._?Ca("Already initialized"):i.push(ka(e||null))}),$a=(e,t)=>{return n=t,o=_a("metadata"),i=e[3],o in n?wa(n,o,{enumerable:!0,configurable:!0,writable:!0,value:i}):n[o]=i;var n,o,i},La=(e,t,n,o)=>{for(var i=0,s=e[t>>1],r=s&&s.length;i<r;i++)1&t?s[i].call(n):o=s[i].call(n,o);return o},Pa=(e,t,n,o,i,s)=>{var r,l,a,c,u,d=7&t,p=!!(8&t),h=!!(16&t),f=d>3?e.length+1:d?p?1:2:0,g=Sa[d+5],m=d>3&&(e[f-1]=[]),v=e[f]||(e[f]=[]),y=d&&(!h&&!p&&(i=i.prototype),d<5&&(d>3||!h)&&ba(d<4?i:{get[n](){return Aa(this,s)},set[n](e){return Ea(this,s,e)}},n));d?h&&d<4&&xa(s,(d>2?"set ":d>1?"get ":"")+n):xa(i,n);for(var w=o.length-1;w>=0;w--)c=Ta(d,n,a={},e[3],v),d&&(c.static=p,c.private=h,u=c.access={has:h?e=>Ma(i,e):e=>n in e},3^d&&(u.get=h?e=>(1^d?Aa:Da)(e,i,4^d?s:y.get):e=>e[n]),d>2&&(u.set=h?(e,t)=>Ea(e,i,t,4^d?s:y.set):(e,t)=>e[n]=t)),l=(0,o[w])(d?d<4?h?s:y[g]:d>4?void 0:{get:y.get,set:y.set}:i,c),a._=1,4^d||void 0===l?ka(l)&&(d>4?m.unshift(l):d?h?s=l:y[g]=l:i=l):"object"!=typeof l||null===l?Ca("Object expected"):(ka(r=l.get)&&(y.get=r),ka(r=l.set)&&(y.set=r),ka(r=l.init)&&m.unshift(r));return d||$a(e,i),y&&wa(i,n,y),h?4^d?s:y:i},Oa=(e,t,n)=>t.has(e)||Ca("Cannot "+n),Ma=(e,t)=>Object(t)!==t?Ca('Cannot use the "in" operator on this value'):e.has(t),Aa=(e,t,n)=>(Oa(e,t,"read from private field"),n?n.call(e):t.get(e)),Ea=(e,t,n,o)=>(Oa(e,t,"write to private field"),o?o.call(e,n):t.set(e,n),n),Da=(e,t,n)=>(Oa(e,t,"access private method"),n);ma=[Bs({components:{IconLamp:Gs,IconTriggerBack:Ks},directives:{drag:Us}})];let Ra=class extends(ga=Ss,fa=[Ds("triggerDiv")],ga){constructor(){super(...arguments),this.triggerDiv=La(va,8,this),La(va,11,this),this.headerHeightPx=62,this.initialized=!1,this.triggerStyle=void 0,this.triggerStyleCorrect=void 0,this.handleResize=void 0}t(e){return window.lang.t(e)}onChangePosition(){(new Ws).send("saveButtonPosition",this.triggerStyle)}getComputedTriggerStyle(){let e={};for(let t in this.triggerStyleCorrect)e[t]=this.triggerStyleCorrect[t]+"px";return e}handleDrag(e,t){!function(){for(let e=0;e<pl.length;e++)pl[e].hide()}(),this.triggerStyle=this.triggerStyleCorrect,this.triggerStyle.bottom=this.triggerStyle.bottom-e.y,this.calculateStyleCorrect()}toggleChat(){Is.isActive?Is.hide():Is.show()}mounted(){this.initialized||this.initialize(),this.handleResize=()=>this.calculateStyleCorrect(),this.handleResize(),window.addEventListener("resize",this.handleResize),this.triggerDiv.addEventListener("click",(()=>{this.toggleChat()})),$(this.triggerDiv).trigger("new-content")}calculateStyleCorrect(){let e={...this.triggerStyle},t=document.documentElement.clientHeight-this.headerHeightPx,n=this.triggerDiv.clientHeight;e.bottom+n>t&&(e.bottom=t-n),e.bottom<0&&(e.bottom=0),this.triggerStyleCorrect=e}initialize(){Object.keys(window.global.ai.chatSettings).length&&null!==window.global.ai.chatSettings.trigger_bottom?this.triggerStyle={bottom:window.global.ai.chatSettings.trigger_bottom}:this.triggerStyle={bottom:100},this.calculateStyleCorrect(),this.initialized=!0}};var Ha;Pa(va=[,,,ya((null==(Ha=ga)?void 0:Ha[_a("metadata")])??null)],5,"triggerDiv",fa,Ra),Ra=Pa(va,0,"TriggerButton",ma,Ra),La(va,1,Ra);const ja=Zs(Fs(Ra),[["render",function(e,t,n,o,i,s){const r=xn("icon-trigger-back"),l=xn("icon-lamp"),a=kn("drag"),c=kn("tooltip");return Kt((Io(),qo("div",{class:"ai-trigger",style:B(e.getComputedTriggerStyle()),ref:"triggerDiv",onDragend:t[0]||(t[0]=(...t)=>e.onChangePosition&&e.onChangePosition(...t))},[ei(r),ei(l)],36)),[[a,{params:{},handleDrag:e.handleDrag}],[c,"Чат с ИИ",void 0,{left:!0}]])}],["__scopeId","data-v-c1a429e9"]]);class za{constructor(e,t,n,o=new Date){this.text=e,this.role=t,this.status=n,this.datetime=o,this.viewMode="text"}formattedDate(){return this.datetime.toLocaleDateString("ru-RU")}getTextByBlocks(){const e=[],t=/```(\w+)?([\s\S]*?)```/g;let n,o=0,i=this.text;for(;null!==(n=t.exec(i));)n.index>o&&e.push({type:"text",lang:"",content:i.substring(o,n.index)}),void 0===n[1]&&n[2].indexOf("<html")>-1&&(n[1]="html"),e.push({type:"code",lang:n[1],content:n[2]}),o=t.lastIndex;return o<i.length&&e.push({type:"text",lang:"",content:i.substring(o)}),0===e.length&&i.length>0&&e.push({type:"text",lang:"",content:i}),e}hasHtml(){return null!==this.text.match(/```(html)\s*([\s\S]*?)(```|$)/g)}import(e){this.text=e.message,this.role=e.role,this.datetime=new Date(e.date_of_create)}}const Ba={baseProfile:"tiny",height:"24px",version:"1.2",viewBox:"0 0 24 24",width:"24px","xml:space":"preserve",xmlns:"http://www.w3.org/2000/svg","xmlns:xlink":"http://www.w3.org/1999/xlink"};const Na=Zs({},[["render",function(e,t){return Io(),qo("svg",Ba,t[0]||(t[0]=[Jo("g",{id:"Layer_1"},[Jo("path",{d:"M13,5.586l-4.707,4.707c-0.391,0.391-0.391,1.023,0,1.414s1.023,0.391,1.414,0L12,9.414V17c0,0.552,0.447,1,1,1   s1-0.448,1-1V9.414l2.293,2.293C16.488,11.902,16.744,12,17,12s0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414L13,5.586z",fill:"CurrentColor"})],-1)]))}]]),Fa={viewBox:"0 0 256 256",xmlns:"http://www.w3.org/2000/svg",width:"24px",height:"24px"};const Va=Zs({},[["render",function(e,t){return Io(),qo("svg",Fa,t[0]||(t[0]=[Jo("rect",{fill:"none",height:"256",width:"256"},null,-1),Jo("rect",{height:"168",rx:"14.9",width:"168",x:"44",y:"44",fill:"CurrentColor"},null,-1)]))}]]),Ia={fill:"none",height:"15",viewBox:"0 0 15 15",width:"15",xmlns:"http://www.w3.org/2000/svg"};const Ua=Zs({},[["render",function(e,t){return Io(),qo("svg",Ia,t[0]||(t[0]=[Jo("path",{"clip-rule":"evenodd",d:"M4.49999 2.5C4.49999 1.94772 4.9477 1.5 5.49999 1.5C6.05227 1.5 6.49999 1.94772 6.49999 2.5C6.49999 3.05228 6.05227 3.5 5.49999 3.5C4.9477 3.5 4.49999 3.05228 4.49999 2.5ZM8.49999 2.5C8.49999 1.94772 8.9477 1.5 9.49999 1.5C10.0523 1.5 10.5 1.94772 10.5 2.5C10.5 3.05228 10.0523 3.5 9.49999 3.5C8.9477 3.5 8.49999 3.05229 8.49999 2.5ZM4.49998 7.5C4.49998 6.94772 4.9477 6.5 5.49998 6.5C6.05227 6.5 6.49998 6.94772 6.49998 7.5C6.49998 8.05228 6.05227 8.5 5.49998 8.5C4.9477 8.5 4.49998 8.05228 4.49998 7.5ZM8.49998 7.5C8.49998 6.94771 8.9477 6.5 9.49999 6.5C10.0523 6.5 10.5 6.94772 10.5 7.5C10.5 8.05228 10.0523 8.5 9.49998 8.5C8.9477 8.5 8.49998 8.05228 8.49998 7.5ZM4.49998 12.5C4.49998 11.9477 4.9477 11.5 5.49998 11.5C6.05227 11.5 6.49998 11.9477 6.49998 12.5C6.49998 13.0523 6.05227 13.5 5.49998 13.5C4.9477 13.5 4.49998 13.0523 4.49998 12.5ZM8.49998 12.5C8.49998 11.9477 8.9477 11.5 9.49998 11.5C10.0523 11.5 10.5 11.9477 10.5 12.5C10.5 13.0523 10.0523 13.5 9.49998 13.5C8.9477 13.5 8.49998 13.0523 8.49998 12.5Z",fill:"black","fill-rule":"evenodd"},null,-1)]))}]]),Wa={fill:"none",height:"24",viewBox:"0 0 24 24",width:"24",xmlns:"http://www.w3.org/2000/svg"};const Za=Zs({},[["render",function(e,t){return Io(),qo("svg",Wa,t[0]||(t[0]=[Jo("path",{d:"M4.2097 4.3871L4.29289 4.29289C4.65338 3.93241 5.22061 3.90468 5.6129 4.2097L5.70711 4.29289L12 10.585L18.2929 4.29289C18.6834 3.90237 19.3166 3.90237 19.7071 4.29289C20.0976 4.68342 20.0976 5.31658 19.7071 5.70711L13.415 12L19.7071 18.2929C20.0676 18.6534 20.0953 19.2206 19.7903 19.6129L19.7071 19.7071C19.3466 20.0676 18.7794 20.0953 18.3871 19.7903L18.2929 19.7071L12 13.415L5.70711 19.7071C5.31658 20.0976 4.68342 20.0976 4.29289 19.7071C3.90237 19.3166 3.90237 18.6834 4.29289 18.2929L10.585 12L4.29289 5.70711C3.93241 5.34662 3.90468 4.77939 4.2097 4.3871L4.29289 4.29289L4.2097 4.3871Z",fill:"currentColor"},null,-1)]))}]]),qa={fill:"none",height:"24",viewBox:"0 0 24 24",width:"24",xmlns:"http://www.w3.org/2000/svg"};const Ga=Zs({},[["render",function(e,t){return Io(),qo("svg",qa,t[0]||(t[0]=[Jo("path",{d:"M19.25 4C20.7688 4 22 5.23122 22 6.75V17.25C22 18.7688 20.7688 20 19.25 20H4.75C3.23122 20 2 18.7688 2 17.25V6.75C2 5.23122 3.23122 4 4.75 4H19.25ZM15 18.5V5.5H4.75C4.05964 5.5 3.5 6.05964 3.5 6.75V17.25C3.5 17.9404 4.05964 18.5 4.75 18.5H15Z",fill:"currentColor"},null,-1)]))}]]),Ya={fill:"none",height:"24",viewBox:"0 0 24 24",width:"24",xmlns:"http://www.w3.org/2000/svg"};const Ka=Zs({},[["render",function(e,t){return Io(),qo("svg",Ya,t[0]||(t[0]=[Jo("path",{d:"M6.75 3.93457C5.23122 3.93457 4 5.16579 4 6.68457V16.2502C4 16.6645 4.33579 17.0002 4.75 17.0002C5.16421 17.0002 5.5 16.6645 5.5 16.2502V6.68457C5.5 5.99421 6.05964 5.43457 6.75 5.43457H16.25C16.9404 5.43457 17.5 5.99421 17.5 6.68457V11.2776C17.9734 11.069 18.4893 10.9778 19 11.0052V6.68457C19 5.16579 17.7688 3.93457 16.25 3.93457H6.75Z",fill:"currentColor"},null,-1),Jo("path",{d:"M20.3063 12.5575L20.1773 12.4517L20.1742 12.4494C19.2943 11.7823 18.0073 11.8628 17.2228 12.6611L13.4197 16.5305C13.1842 16.7701 13.0116 17.0599 12.9166 17.3759L12.3303 19.3283C11.764 19.4144 11.261 19.4066 10.7256 19.3216C10.7015 19.3178 10.6879 19.2919 10.6983 19.2699L10.7325 19.1976L10.7355 19.1912C10.8006 19.053 10.9155 18.8092 10.9528 18.5583C10.9736 18.4184 10.9795 18.2242 10.904 18.0175C10.8229 17.7952 10.6707 17.622 10.4818 17.5059C10.1584 17.307 9.75504 17.2918 9.40872 17.3118C9.13449 17.3276 8.79514 17.3742 8.37789 17.4556C7.91595 17.5456 7.47693 17.7523 7.03945 17.9583C6.46264 18.23 5.88848 18.5003 5.26798 18.5003C5.04503 18.5003 4.8306 18.4639 4.63034 18.3965C4.50723 18.3552 4.37174 18.455 4.40299 18.581C4.45965 18.8096 4.56469 19.1185 4.79525 19.3564C4.92127 19.4864 5.09027 19.6003 5.30333 19.6551C5.51538 19.7095 5.72083 19.6925 5.90086 19.6396C7.35473 19.2128 8.3205 18.9809 8.94874 18.8759C9.08062 18.8539 9.17967 18.9871 9.13919 19.1146C9.07578 19.3142 8.98619 19.6924 9.17263 20.0751C9.37486 20.4902 9.77319 20.664 10.1174 20.7349C11.2602 20.97 12.2462 20.9394 13.4527 20.6227C13.5054 20.6142 13.5581 20.6016 13.6102 20.5847L13.7274 20.5468C13.8985 20.4973 14.0743 20.4423 14.2558 20.3818C14.2831 20.3727 14.3095 20.3621 14.3351 20.3503L15.7228 19.9015C16.0608 19.7922 16.3658 19.6049 16.6105 19.356L20.4049 15.4956C21.1911 14.6948 21.1877 13.4614 20.4233 12.6701L20.3063 12.5575Z",fill:"currentColor"},null,-1)]))}]]);const Xa=Zs({},[["render",function(e,t){return t[0]||(t[0]=ii('<svg class="ai-icon" fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M6.50244 3.00293C4.56944 3.00293 3.00244 4.56993 3.00244 6.50293V12.5029C3.00244 14.4359 4.56944 16.0029 6.50244 16.0029H6.99853V14.0029H6.50244C5.67401 14.0029 5.00244 13.3314 5.00244 12.5029V6.50293C5.00244 5.6745 5.67401 5.00293 6.50244 5.00293H12.5024C13.3309 5.00293 14.0024 5.6745 14.0024 6.50293V12.5029C14.0024 13.3314 13.3309 14.0029 12.5024 14.0029H10.9957V16.0029H12.5024C14.4354 16.0029 16.0024 14.4359 16.0024 12.5029V6.50293C16.0024 4.56993 14.4354 3.00293 12.5024 3.00293H6.50244Z" fill="currentColor"></path><path d="M10 11.5004C10 10.672 10.6716 10.0004 11.5 10.0004H12.9988V8.00043H11.5C9.567 8.00043 8 9.56743 8 11.5004V17.5004C8 19.4334 9.567 21.0004 11.5 21.0004H17.5C19.433 21.0004 21 19.4334 21 17.5004V11.5004C21 9.56743 19.433 8.00043 17.5 8.00043H17.0049V10.0004H17.5C18.3284 10.0004 19 10.672 19 11.5004V17.5004C19 18.3289 18.3284 19.0004 17.5 19.0004H11.5C10.6716 19.0004 10 18.3289 10 17.5004V11.5004Z" fill="currentColor"></path></svg><svg class="ai-success-icon" fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M8.5 16.5858L4.70711 12.7929C4.31658 12.4024 3.68342 12.4024 3.29289 12.7929C2.90237 13.1834 2.90237 13.8166 3.29289 14.2071L7.79289 18.7071C8.18342 19.0976 8.81658 19.0976 9.20711 18.7071L20.2071 7.70711C20.5976 7.31658 20.5976 6.68342 20.2071 6.29289C19.8166 5.90237 19.1834 5.90237 18.7929 6.29289L8.5 16.5858Z" fill="currentColor"></path></svg>',2))}]]),Qa={fill:"none",height:"24",viewBox:"0 0 24 24",width:"24",xmlns:"http://www.w3.org/2000/svg"};const Ja=Zs({},[["render",function(e,t){return Io(),qo("svg",Qa,t[0]||(t[0]=[Jo("path",{d:"M20.0078 6.5C20.0078 5.11929 18.8885 4 17.5078 4H6.50781C5.1271 4 4.00781 5.11929 4.00781 6.5L4.00781 10.5C4.00781 11.0523 4.45553 11.5 5.00781 11.5C5.5601 11.5 6.00781 11.0523 6.00781 10.5L6.00781 6.5C6.00781 6.22386 6.23167 6 6.50781 6L17.5078 6C17.784 6 18.0078 6.22386 18.0078 6.5V10.5C18.0078 11.0523 18.4555 11.5 19.0078 11.5C19.5601 11.5 20.0078 11.0523 20.0078 10.5V6.5ZM18.2071 19.7071L20.7071 17.2071C21.0976 16.8166 21.0976 16.1834 20.7071 15.7929L18.2071 13.2929C17.8166 12.9024 17.1834 12.9024 16.7929 13.2929C16.4024 13.6834 16.4024 14.3166 16.7929 14.7071L18.5858 16.5L16.7929 18.2929C16.4024 18.6834 16.4024 19.3166 16.7929 19.7071C17.1834 20.0976 17.8166 20.0976 18.2071 19.7071ZM3.29289 15.7929C2.90237 16.1834 2.90237 16.8166 3.29289 17.2071L5.79289 19.7071C6.18342 20.0976 6.81658 20.0976 7.20711 19.7071C7.59763 19.3166 7.59763 18.6834 7.20711 18.2929L5.41421 16.5L7.20711 14.7071C7.59763 14.3166 7.59763 13.6834 7.20711 13.2929C6.81658 12.9024 6.18342 12.9024 5.79289 13.2929L3.29289 15.7929ZM13 16.5C13 15.9477 12.5523 15.5 12 15.5C11.4477 15.5 11 15.9477 11 16.5C11 17.0523 11.4477 17.5 12 17.5C12.5523 17.5 13 17.0523 13 16.5ZM9 15.5C9.55228 15.5 10 15.9477 10 16.5C10 17.0523 9.55228 17.5 9 17.5H8C7.44772 17.5 7 17.0523 7 16.5C7 15.9477 7.44772 15.5 8 15.5H9ZM17 16.5C17 15.9477 16.5523 15.5 16 15.5H15C14.4477 15.5 14 15.9477 14 16.5C14 17.0523 14.4477 17.5 15 17.5H16C16.5523 17.5 17 17.0523 17 16.5Z",fill:"currentColor"},null,-1)]))}]]);var ec,tc,nc,oc,ic,sc=Object.create,rc=Object.defineProperty,lc=Object.getOwnPropertyDescriptor,ac=(e,t)=>(t=Symbol[e])?t:Symbol.for("Symbol."+e),cc=e=>{throw TypeError(e)},uc=(e,t)=>rc(e,"name",{value:t,configurable:!0}),dc=["class","method","getter","setter","accessor","field","value","get","set"],pc=e=>void 0!==e&&"function"!=typeof e?cc("Function expected"):e,hc=(e,t,n,o,i)=>({kind:dc[e],name:t,metadata:o,addInitializer:e=>n._?cc("Already initialized"):i.push(pc(e||null))}),fc=(e,t)=>{return n=t,o=ac("metadata"),i=e[3],o in n?rc(n,o,{enumerable:!0,configurable:!0,writable:!0,value:i}):n[o]=i;var n,o,i},gc=(e,t,n,o)=>{for(var i=0,s=e[t>>1],r=s&&s.length;i<r;i++)1&t?s[i].call(n):o=s[i].call(n,o);return o},mc=(e,t,n,o,i,s)=>{var r,l,a,c,u,d=7&t,p=!!(8&t),h=!!(16&t),f=d>3?e.length+1:d?p?1:2:0,g=dc[d+5],m=d>3&&(e[f-1]=[]),v=e[f]||(e[f]=[]),y=d&&(!h&&!p&&(i=i.prototype),d<5&&(d>3||!h)&&lc(d<4?i:{get[n](){return wc(this,s)},set[n](e){return bc(this,s,e)}},n));d?h&&d<4&&uc(s,(d>2?"set ":d>1?"get ":"")+n):uc(i,n);for(var w=o.length-1;w>=0;w--)c=hc(d,n,a={},e[3],v),d&&(c.static=p,c.private=h,u=c.access={has:h?e=>yc(i,e):e=>n in e},3^d&&(u.get=h?e=>(1^d?wc:_c)(e,i,4^d?s:y.get):e=>e[n]),d>2&&(u.set=h?(e,t)=>bc(e,i,t,4^d?s:y.set):(e,t)=>e[n]=t)),l=(0,o[w])(d?d<4?h?s:y[g]:d>4?void 0:{get:y.get,set:y.set}:i,c),a._=1,4^d||void 0===l?pc(l)&&(d>4?m.unshift(l):d?h?s=l:y[g]=l:i=l):"object"!=typeof l||null===l?cc("Object expected"):(pc(r=l.get)&&(y.get=r),pc(r=l.set)&&(y.set=r),pc(r=l.init)&&m.unshift(r));return d||fc(e,i),y&&rc(i,n,y),h?4^d?s:y:i},vc=(e,t,n)=>t.has(e)||cc("Cannot "+n),yc=(e,t)=>Object(t)!==t?cc('Cannot use the "in" operator on this value'):e.has(t),wc=(e,t,n)=>(vc(e,t,"read from private field"),n?n.call(e):t.get(e)),bc=(e,t,n,o)=>(vc(e,t,"write to private field"),o?o.call(e,n):t.set(e,n),n),_c=(e,t,n)=>(vc(e,t,"access private method"),n);oc=[Bs];let Cc=class extends(nc=Ss,tc=[Rs],ec=[Rs],nc){constructor(){super(...arguments),this.message=gc(ic,8,this),gc(ic,11,this),this.textBlock=gc(ic,12,this),gc(ic,15,this)}getSafeContent(){return this.textBlock.content.trim()}};ic=(e=>[,,,sc((null==e?void 0:e[ac("metadata")])??null)])(nc),mc(ic,5,"message",tc,Cc),mc(ic,5,"textBlock",ec,Cc),Cc=mc(ic,0,"BlockText",oc,Cc),gc(ic,1,Cc);const xc=Fs(Cc),Sc={class:"ai-text ai-pre-wrap"};const kc=Zs(xc,[["render",function(e,t,n,o,i,s){return Io(),qo("div",Sc,Y(e.getSafeContent()),1)}]]);var Tc,$c,Lc,Pc,Oc,Mc,Ac=Object.create,Ec=Object.defineProperty,Dc=Object.getOwnPropertyDescriptor,Rc=(e,t)=>(t=Symbol[e])?t:Symbol.for("Symbol."+e),Hc=e=>{throw TypeError(e)},jc=(e,t)=>Ec(e,"name",{value:t,configurable:!0}),zc=["class","method","getter","setter","accessor","field","value","get","set"],Bc=e=>void 0!==e&&"function"!=typeof e?Hc("Function expected"):e,Nc=(e,t,n,o,i)=>({kind:zc[e],name:t,metadata:o,addInitializer:e=>n._?Hc("Already initialized"):i.push(Bc(e||null))}),Fc=(e,t)=>{return n=t,o=Rc("metadata"),i=e[3],o in n?Ec(n,o,{enumerable:!0,configurable:!0,writable:!0,value:i}):n[o]=i;var n,o,i},Vc=(e,t,n,o)=>{for(var i=0,s=e[t>>1],r=s&&s.length;i<r;i++)1&t?s[i].call(n):o=s[i].call(n,o);return o},Ic=(e,t,n,o,i,s)=>{var r,l,a,c,u,d=7&t,p=!!(8&t),h=!!(16&t),f=d>3?e.length+1:d?p?1:2:0,g=zc[d+5],m=d>3&&(e[f-1]=[]),v=e[f]||(e[f]=[]),y=d&&(!h&&!p&&(i=i.prototype),d<5&&(d>3||!h)&&Dc(d<4?i:{get[n](){return Zc(this,s)},set[n](e){return qc(this,s,e)}},n));d?h&&d<4&&jc(s,(d>2?"set ":d>1?"get ":"")+n):jc(i,n);for(var w=o.length-1;w>=0;w--)c=Nc(d,n,a={},e[3],v),d&&(c.static=p,c.private=h,u=c.access={has:h?e=>Wc(i,e):e=>n in e},3^d&&(u.get=h?e=>(1^d?Zc:Gc)(e,i,4^d?s:y.get):e=>e[n]),d>2&&(u.set=h?(e,t)=>qc(e,i,t,4^d?s:y.set):(e,t)=>e[n]=t)),l=(0,o[w])(d?d<4?h?s:y[g]:d>4?void 0:{get:y.get,set:y.set}:i,c),a._=1,4^d||void 0===l?Bc(l)&&(d>4?m.unshift(l):d?h?s=l:y[g]=l:i=l):"object"!=typeof l||null===l?Hc("Object expected"):(Bc(r=l.get)&&(y.get=r),Bc(r=l.set)&&(y.set=r),Bc(r=l.init)&&m.unshift(r));return d||Fc(e,i),y&&Ec(i,n,y),h?4^d?s:y:i},Uc=(e,t,n)=>t.has(e)||Hc("Cannot "+n),Wc=(e,t)=>Object(t)!==t?Hc('Cannot use the "in" operator on this value'):e.has(t),Zc=(e,t,n)=>(Uc(e,t,"read from private field"),n?n.call(e):t.get(e)),qc=(e,t,n,o)=>(Uc(e,t,"write to private field"),o?o.call(e,n):t.set(e,n),n),Gc=(e,t,n)=>(Uc(e,t,"access private method"),n);Oc=[Bs({components:{IconCopy:Xa}})];let Yc=class extends(Pc=Ss,Lc=[Rs],$c=[Rs],Tc=[Ds],Pc){constructor(){super(...arguments),this.message=Vc(Mc,8,this),Vc(Mc,11,this),this.textBlock=Vc(Mc,12,this),Vc(Mc,15,this),this.preview=Vc(Mc,16,this),Vc(Mc,19,this),this.viewAs="raw"}copy(e){if("raw"==this.viewAs)navigator.clipboard.writeText(this.textBlock.content);else{const e=this.preview.innerHTML,t=new Blob([e],{type:"text/html"}),n=e.replace(/<[^>]*>/g,""),o=new Blob([n],{type:"text/plain"});navigator.clipboard.write([new ClipboardItem({"text/html":t,"text/plain":o})])}const t=e.target.closest("button");t.classList.add("ai-success"),setTimeout((()=>{t.classList.remove("ai-success")}),1e3)}switchView(e){this.viewAs=e}getRawContent(){return function(e){const t={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;","`":"&#x60;","=":"&#x3D;"};return e.replace(/[&<>"'`=/]/g,(e=>t[e]||e))}(this.textBlock.content).trim()}getPreviewContent(){return this.textBlock.content.trim()}};Mc=(e=>[,,,Ac((null==e?void 0:e[Rc("metadata")])??null)])(Pc),Ic(Mc,5,"message",Lc,Yc),Ic(Mc,5,"textBlock",$c,Yc),Ic(Mc,5,"preview",Tc,Yc),Yc=Ic(Mc,0,"BlockCode",Oc,Yc),Vc(Mc,1,Yc);const Kc=Fs(Yc),Xc=["data-lang"],Qc={class:"ai-code-actions"},Jc={key:0,class:"ai-switch-view"},eu={class:"ai-code-content"},tu={key:0,class:"ai-raw ai-pre-wrap"},nu={key:1,class:"ai-preview",ref:"preview"};const ou=Zs(Kc,[["render",function(e,t,n,o,i,s){const r=xn("icon-copy"),l=kn("tooltip"),a=kn("html-safe");return Io(),qo("div",{class:"ai-code","data-lang":e.textBlock.lang},[Jo("div",Qc,[t[3]||(t[3]=Jo("div",{class:"ai-code-caption"},"Код",-1)),"html"==e.textBlock.lang?(Io(),qo("div",Jc,[Jo("button",{onClick:t[0]||(t[0]=t=>e.switchView("raw")),class:U({active:"raw"==this.viewAs})},"Текст",2),"html"==e.textBlock.lang?(Io(),qo("button",{key:0,onClick:t[1]||(t[1]=t=>e.switchView("preview")),class:U({active:"preview"==this.viewAs})},"Html",2)):si("",!0)])):si("",!0),Kt((Io(),qo("button",{class:"ai-button-copy",onClick:t[2]||(t[2]=t=>e.copy(t))},[ei(r)])),[[l,"Копировать"]])]),Jo("div",eu,["raw"==e.viewAs?Kt((Io(),qo("div",tu,null,512)),[[a,e.getRawContent()]]):si("",!0),"preview"==e.viewAs?Kt((Io(),qo("div",nu,null,512)),[[a,e.getPreviewContent()]]):si("",!0)])],8,Xc)}],["__scopeId","data-v-dae805e8"]]),iu={width:"24",height:"19",viewBox:"0 0 24 19",xmlns:"http://www.w3.org/2000/svg"};const su=Zs({},[["render",function(e,t){const n=xn("svg:style");return Io(),qo("svg",iu,[ei(n,null,{default:Yt((()=>t[0]||(t[0]=[oi(" circle { fill: #888; opacity: 0.9; } .dot1 { animation: move-dots 1s infinite linear; } .dot2 { animation: move-dots 1s infinite linear 0.33s; } .dot3 { animation: move-dots 1s infinite linear 0.66s; } @keyframes move-dots { 0%, 100% { transform: translateY(0); } 20% { transform: translateY(-5px); } 40% { transform: translateY(5px); } 60% { transform: translateY(0); } 80% { transform: translateY(-5px); } } ")]))),_:1,__:[0]}),t[1]||(t[1]=Jo("circle",{class:"dot1",cx:"4",cy:"9.5",r:"3.5"},null,-1)),t[2]||(t[2]=Jo("circle",{class:"dot2",cx:"12",cy:"9.5",r:"3.5"},null,-1)),t[3]||(t[3]=Jo("circle",{class:"dot3",cx:"20",cy:"9.5",r:"3.5"},null,-1))])}]]);const ru=Zs({},[["render",function(e,t){return Io(),qo(jo,null,[t[0]||(t[0]=Jo("svg",{class:"ai-icon",fill:"none",height:"24",viewBox:"0 0 24 24",width:"24",xmlns:"http://www.w3.org/2000/svg"},[Jo("path",{d:"M16.0518 5.0285C15.7169 5.46765 15.8013 6.09515 16.2405 6.43007C17.9675 7.74714 19 9.78703 19 12C19 15.4973 16.4352 18.3956 13.084 18.9166L13.7929 18.2071C14.1834 17.8166 14.1834 17.1834 13.7929 16.7929C13.4024 16.4024 12.7692 16.4024 12.3787 16.7929L9.87868 19.2929C9.48816 19.6834 9.48816 20.3166 9.87868 20.7071L12.3787 23.2071C12.7692 23.5976 13.4024 23.5976 13.7929 23.2071C14.1834 22.8166 14.1834 22.1834 13.7929 21.7929L12.9497 20.9505C17.4739 20.476 21 16.6498 21 12C21 9.15644 19.6712 6.53122 17.4533 4.83978C17.0142 4.50486 16.3867 4.58936 16.0518 5.0285ZM14.1213 3.29289L11.6213 0.792893C11.2308 0.402369 10.5976 0.402369 10.2071 0.792893C9.84662 1.15338 9.81889 1.72061 10.1239 2.1129L10.2071 2.20711L11.0503 3.04951C6.52615 3.52399 3 7.35021 3 12C3 14.7198 4.21515 17.2432 6.2716 18.9419C6.6974 19.2936 7.32771 19.2335 7.67943 18.8077C8.03116 18.3819 7.97111 17.7516 7.54531 17.3999C5.94404 16.0772 5 14.1168 5 12C5 8.50269 7.56475 5.60441 10.916 5.08343L10.2071 5.79289C9.81658 6.18342 9.81658 6.81658 10.2071 7.20711C10.5676 7.56759 11.1348 7.59532 11.5271 7.2903L11.6213 7.20711L14.1213 4.70711C14.4818 4.34662 14.5095 3.77939 14.2045 3.3871L14.1213 3.29289Z",fill:"currentColor"})],-1)),t[1]||(t[1]=Jo("svg",{class:"ai-success-icon",fill:"none",height:"24",viewBox:"0 0 24 24",width:"24",xmlns:"http://www.w3.org/2000/svg"},[Jo("path",{d:"M8.5 16.5858L4.70711 12.7929C4.31658 12.4024 3.68342 12.4024 3.29289 12.7929C2.90237 13.1834 2.90237 13.8166 3.29289 14.2071L7.79289 18.7071C8.18342 19.0976 8.81658 19.0976 9.20711 18.7071L20.2071 7.70711C20.5976 7.31658 20.5976 6.68342 20.2071 6.29289C19.8166 5.90237 19.1834 5.90237 18.7929 6.29289L8.5 16.5858Z",fill:"currentColor"})],-1))],64)}]]),lu={class:"feather-alert-triangle",fill:"none",height:"18",stroke:"currentColor","stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",viewBox:"0 0 24 24",width:"18",xmlns:"http://www.w3.org/2000/svg"};const au=Zs({},[["render",function(e,t){return Io(),qo("svg",lu,t[0]||(t[0]=[Jo("path",{d:"M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"},null,-1),Jo("line",{x1:"12",x2:"12",y1:"9",y2:"13"},null,-1),Jo("line",{x1:"12",x2:"12.01",y1:"17",y2:"17"},null,-1)]))}]]),cu={"enable-background":"new 0 0 32 32",height:"18",id:"Stock_cut",version:"1.1",viewBox:"0 0 32 32","xml:space":"preserve",xmlns:"http://www.w3.org/2000/svg","xmlns:xlink":"http://www.w3.org/1999/xlink"};const uu=Zs({},[["render",function(e,t){return Io(),qo("svg",cu,t[0]||(t[0]=[ii('<desc></desc><g><path d="M23,12V3   c0-1.105-0.895-2-2-2H3C1.895,1,1,1.895,1,3v22c0,1.105,0.895,2,2,2h9" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2"></path><path d="M7,10H1v8h6   c2.209,0,4-1.791,4-4v0C11,11.791,9.209,10,7,10z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2"></path><circle cx="7" cy="14" r="1"></circle><circle cx="23" cy="23" fill="none" r="8" stroke="currentColor" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2"></circle><line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2" x1="18" x2="28" y1="23" y2="23"></line><line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="2" x1="23" x2="23" y1="18" y2="28"></line></g>',2)]))}]]);const du=Zs({},[["render",function(e,t){return Io(),qo(jo,null,[t[0]||(t[0]=Jo("svg",{class:"ai-icon",fill:"none",height:"18",viewBox:"0 0 24 24",width:"18",xmlns:"http://www.w3.org/2000/svg"},[Jo("path",{d:"M16.0518 5.0285C15.7169 5.46765 15.8013 6.09515 16.2405 6.43007C17.9675 7.74714 19 9.78703 19 12C19 15.4973 16.4352 18.3956 13.084 18.9166L13.7929 18.2071C14.1834 17.8166 14.1834 17.1834 13.7929 16.7929C13.4024 16.4024 12.7692 16.4024 12.3787 16.7929L9.87868 19.2929C9.48816 19.6834 9.48816 20.3166 9.87868 20.7071L12.3787 23.2071C12.7692 23.5976 13.4024 23.5976 13.7929 23.2071C14.1834 22.8166 14.1834 22.1834 13.7929 21.7929L12.9497 20.9505C17.4739 20.476 21 16.6498 21 12C21 9.15644 19.6712 6.53122 17.4533 4.83978C17.0142 4.50486 16.3867 4.58936 16.0518 5.0285ZM14.1213 3.29289L11.6213 0.792893C11.2308 0.402369 10.5976 0.402369 10.2071 0.792893C9.84662 1.15338 9.81889 1.72061 10.1239 2.1129L10.2071 2.20711L11.0503 3.04951C6.52615 3.52399 3 7.35021 3 12C3 14.7198 4.21515 17.2432 6.2716 18.9419C6.6974 19.2936 7.32771 19.2335 7.67943 18.8077C8.03116 18.3819 7.97111 17.7516 7.54531 17.3999C5.94404 16.0772 5 14.1168 5 12C5 8.50269 7.56475 5.60441 10.916 5.08343L10.2071 5.79289C9.81658 6.18342 9.81658 6.81658 10.2071 7.20711C10.5676 7.56759 11.1348 7.59532 11.5271 7.2903L11.6213 7.20711L14.1213 4.70711C14.4818 4.34662 14.5095 3.77939 14.2045 3.3871L14.1213 3.29289Z",fill:"currentColor"})],-1)),t[1]||(t[1]=Jo("svg",{class:"ai-success-icon",fill:"none",height:"18",viewBox:"0 0 24 24",width:"18",xmlns:"http://www.w3.org/2000/svg"},[Jo("path",{d:"M8.5 16.5858L4.70711 12.7929C4.31658 12.4024 3.68342 12.4024 3.29289 12.7929C2.90237 13.1834 2.90237 13.8166 3.29289 14.2071L7.79289 18.7071C8.18342 19.0976 8.81658 19.0976 9.20711 18.7071L20.2071 7.70711C20.5976 7.31658 20.5976 6.68342 20.2071 6.29289C19.8166 5.90237 19.1834 5.90237 18.7929 6.29289L8.5 16.5858Z",fill:"currentColor"})],-1))],64)}]]);var pu,hu,fu,gu,mu,vu=Object.create,yu=Object.defineProperty,wu=Object.getOwnPropertyDescriptor,bu=(e,t)=>(t=Symbol[e])?t:Symbol.for("Symbol."+e),_u=e=>{throw TypeError(e)},Cu=(e,t)=>yu(e,"name",{value:t,configurable:!0}),xu=["class","method","getter","setter","accessor","field","value","get","set"],Su=e=>void 0!==e&&"function"!=typeof e?_u("Function expected"):e,ku=(e,t,n,o,i)=>({kind:xu[e],name:t,metadata:o,addInitializer:e=>n._?_u("Already initialized"):i.push(Su(e||null))}),Tu=(e,t)=>{return n=t,o=bu("metadata"),i=e[3],o in n?yu(n,o,{enumerable:!0,configurable:!0,writable:!0,value:i}):n[o]=i;var n,o,i},$u=(e,t,n,o)=>{for(var i=0,s=e[t>>1],r=s&&s.length;i<r;i++)1&t?s[i].call(n):o=s[i].call(n,o);return o},Lu=(e,t,n,o,i,s)=>{var r,l,a,c,u,d=7&t,p=!!(8&t),h=!!(16&t),f=d>3?e.length+1:d?p?1:2:0,g=xu[d+5],m=d>3&&(e[f-1]=[]),v=e[f]||(e[f]=[]),y=d&&(!h&&!p&&(i=i.prototype),d<5&&(d>3||!h)&&wu(d<4?i:{get[n](){return Mu(this,s)},set[n](e){return Au(this,s,e)}},n));d?h&&d<4&&Cu(s,(d>2?"set ":d>1?"get ":"")+n):Cu(i,n);for(var w=o.length-1;w>=0;w--)c=ku(d,n,a={},e[3],v),d&&(c.static=p,c.private=h,u=c.access={has:h?e=>Ou(i,e):e=>n in e},3^d&&(u.get=h?e=>(1^d?Mu:Eu)(e,i,4^d?s:y.get):e=>e[n]),d>2&&(u.set=h?(e,t)=>Au(e,i,t,4^d?s:y.set):(e,t)=>e[n]=t)),l=(0,o[w])(d?d<4?h?s:y[g]:d>4?void 0:{get:y.get,set:y.set}:i,c),a._=1,4^d||void 0===l?Su(l)&&(d>4?m.unshift(l):d?h?s=l:y[g]=l:i=l):"object"!=typeof l||null===l?_u("Object expected"):(Su(r=l.get)&&(y.get=r),Su(r=l.set)&&(y.set=r),Su(r=l.init)&&m.unshift(r));return d||Tu(e,i),y&&yu(i,n,y),h?4^d?s:y:i},Pu=(e,t,n)=>t.has(e)||_u("Cannot "+n),Ou=(e,t)=>Object(t)!==t?_u('Cannot use the "in" operator on this value'):e.has(t),Mu=(e,t,n)=>(Pu(e,t,"read from private field"),n?n.call(e):t.get(e)),Au=(e,t,n,o)=>(Pu(e,t,"write to private field"),o?o.call(e,n):t.set(e,n),n),Eu=(e,t,n)=>(Pu(e,t,"access private method"),n);gu=[Bs({components:{IconSubmit:Na,IconStop:Va,IconDrag:Ua,IconClose:Za,IconPlaceRight:Ga,IconClean:Ka,IconCopy:Xa,IconSwitch:Ja,IconLoader:su,IconRefresh:ru,IconAlert:au,IconRefill:uu,IconRefreshSmall:du,BlockText:kc,BlockCode:ou},directives:{drag:Us}})];let Du=class extends(fu=Ss,hu=[Ds("messages")],pu=[Ds("message-textarea")],fu){constructor(){super(...arguments),this.commonStore=Is,this.formState="blocked",this.question="",this.initialized=!1,this.messages=lt([]),this.balance=null,this.balanceRefillUrl=null,this.minWidth=400,this.minHeight=600,this.beforeChatStyle=void 0,this.chatStyle=void 0,this.chatStyleCorrect=void 0,this.handleResize=void 0,this.messagesRef=$u(mu,8,this),$u(mu,11,this),this.messageTextarea=$u(mu,12,this),$u(mu,15,this),this.abortController=void 0,this.balanceReloading=void 0}t(e){return window.lang.t(e)}switchView(e){e.viewMode="text"==e.viewMode?"html":"text"}onChangePosition(){(new Ws).send("savePosition",this.chatStyle)}cleanChat(){(new Ws).send("cleanChat",{}),this.messages=[]}repeat(e,t){this.messages.splice(t,1),this.getAnswer(!0)}sendQuestion(){let e=this.question;if("ok"!=this.formState||""==e.trim())return;this.question="",this.formState="loading";let t=lt(new za(e,"user","loading",new Date));this.messages.push(t),this.scrollToEnd();let n=new Ws;this.abortController=n.getAbortController(),n.send("saveQuestion",t).then((()=>{t.status="ok",this.getAnswer()})).catch((e=>{t.status="fail",this.formState="ok"}))}getAnswer(e){let t=lt(new za("","assistant","loading",new Date));this.messages.push(t),this.scrollToEnd();let n=new Ws;this.abortController=n.getAbortController(),n.setStreamCallback((e=>{t.text=e})),n.send("getAnswer",{repeat:+e}).then((e=>{e.error&&(t.role="system",t.text=e.error),e.lastChunk&&null!=e.lastChunk.balance&&(this.balance=e.lastChunk.balance,window.dispatchEvent(new CustomEvent("gptBalanceChange",{bubbles:!0,detail:{balance:this.balance,source:"chat"}}))),t.status="ok",this.abortController=null,this.formState="ok"})).catch((e=>{t.status="fail",this.abortController=null,this.formState="ok"}))}stopGeneration(){this.abortController&&this.abortController.abort("AbortError")}scrollToEnd(){setTimeout((()=>{this.messagesRef.scrollTo({top:this.messagesRef.scrollHeight,behavior:"smooth"})}),10)}placeRight(){"right"==this.chatStyle.stick&&this.beforeChatStyle?(this.chatStyle={...this.beforeChatStyle},this.beforeChatStyle=null):(this.beforeChatStyle={...this.chatStyle},this.chatStyle.width=450,this.chatStyle.height=document.documentElement.clientHeight,this.chatStyle.right=0,this.chatStyle.top=0,this.chatStyle.stick="right"),this.calculateStyleCorrect(),this.onChangePosition()}handleDrag(e,t){this.chatStyle=this.chatStyleCorrect,this.chatStyle.stick="none",t.x&&(t.x.increase&&(this.chatStyle[t.x.increase]=this.chatStyle[t.x.increase]+e.x),t.x.decrease&&(this.chatStyle[t.x.decrease]=this.chatStyle[t.x.decrease]-e.x)),t.y&&(t.y.increase&&(this.chatStyle[t.y.increase]=this.chatStyle[t.y.increase]+e.y),t.y.decrease&&(this.chatStyle[t.y.decrease]=this.chatStyle[t.y.decrease]-e.y)),this.chatStyle.width<this.minWidth&&(this.chatStyle.width=this.minWidth),this.chatStyle.height<this.minHeight&&(this.chatStyle.height=this.minHeight),this.calculateStyleCorrect()}getComputedChatStyle(){let e={};for(let t in this.chatStyleCorrect)e[t]=this.chatStyleCorrect[t]+"px";return e}calculateStyleCorrect(){let e={...this.chatStyle},t=document.documentElement.clientWidth,n=document.documentElement.clientHeight;e.right<0&&(e.right=0),e.width>t&&(e.width=t),e.right+e.width>t&&e.width<=t&&(e.right=t-e.width),e.top<0&&(e.top=0),"right"==e.stick&&(e.height=n),e.height>n&&(e.height=n),e.top+e.height>n&&e.height<=n&&(e.top=n-e.height),this.chatStyleCorrect=e}copy(e,t){navigator.clipboard.writeText(t.text);const n=e.target.closest("button");n.classList.add("ai-success"),setTimeout((()=>{n.classList.remove("ai-success")}),1e3)}created(){this.initialize()}mounted(){this.handleResize=()=>this.calculateStyleCorrect(),this.handleResize(),window.addEventListener("resize",this.handleResize),this.commonStore.emitter.on("show",(()=>{this.initialized||this.loadLastChat().then((()=>{this.initialized=!0}))})),this.messageTextarea.addEventListener("input",(e=>{e.target.style.height="auto";const t=Math.min(e.target.scrollHeight,200);e.target.style.height=t+"px",e.target.style.overflowY=e.target.scrollHeight>200?"auto":"hidden"})),this.messageTextarea.addEventListener("keypress",(e=>{e.ctrlKey&&"Enter"==e.code&&this.sendQuestion()}))}unmounted(){window.removeEventListener("resize",this.handleResize)}initialize(){Object.keys(window.global.ai.chatSettings).length&&null!==window.global.ai.chatSettings.chat_width?this.chatStyle={width:window.global.ai.chatSettings.chat_width,height:window.global.ai.chatSettings.chat_height,right:window.global.ai.chatSettings.chat_right,top:window.global.ai.chatSettings.chat_top,stick:window.global.ai.chatSettings.chat_stick}:this.chatStyle={width:400,height:document.documentElement.clientHeight,right:0,top:0,stick:"right"},this.calculateStyleCorrect()}refreshBalance(e){this.balanceReloading=!0,(new Ws).send("getBalance").then((e=>{this.balanceReloading=!1,e.success&&(this.balance=e.balance,this.balanceRefillUrl=e.balanceRefillUrl)})).catch((()=>{this.balanceReloading=!1}));let t=e.target.closest("button");t.classList.add("ai-success"),setTimeout((()=>{t.classList.remove("ai-success")}),500)}refillBalance(){window.open(this.balanceRefillUrl,"_blank")}loadLastChat(){return(new Ws).send("startChat").then((e=>{e.success&&(e.chat.messages.forEach((e=>{let t=new za;t.import(e),t.status="ok",this.messages.push(t)})),null!==e.chat.balance&&(this.balance=e.chat.balance,window.addEventListener("gptBalanceChange",(e=>{"chat"!=e.detail.source&&(this.balance=e.detail.balance)}))),null!==e.chat.balanceRefillUrl&&(this.balanceRefillUrl=e.chat.balanceRefillUrl),this.formState="ok")}))}};mu=(e=>[,,,vu((null==e?void 0:e[bu("metadata")])??null)])(fu),Lu(mu,5,"messagesRef",hu,Du),Lu(mu,5,"messageTextarea",pu,Du),Du=Lu(mu,0,"Chat",gu,Du),$u(mu,1,Du);const Ru=Fs(Du),Hu=["data-stick"],ju={class:"ai-resize ai-top"},zu={class:"ai-resize ai-right"},Bu={class:"ai-resize ai-bottom"},Nu={class:"ai-resize ai-left"},Fu={class:"ai-corner ai-left-top"},Vu={class:"ai-corner ai-right-top"},Iu={class:"ai-corner ai-right-bottom"},Uu={class:"ai-corner ai-left-bottom"},Wu={class:"ai-header"},Zu={class:"ai-draggable"},qu={class:"ai-header-buttons"},Gu={class:"ai-messages",ref:"messages"},Yu={class:"ai-message-body"},Ku={class:"ai-message-actions"},Xu={class:"ai-left"},Qu=["onClick"],Ju=["onClick"],ed={class:"ai-right"},td={key:0,class:"ai-message-stub"},nd={key:1,class:"ai-message-stub"},od={class:"ai-footer"},id=["placeholder"],sd={key:0,class:"ai-balance"},rd={class:"ai-balance-value"},ld=["disabled"];const ad=Zs(Ru,[["render",function(e,t,n,o,i,s){const r=xn("icon-drag"),l=xn("icon-clean"),a=xn("icon-place-right"),c=xn("icon-close"),u=xn("icon-loader"),d=xn("icon-alert"),p=xn("blockCode"),h=xn("blockText"),f=xn("icon-copy"),g=xn("icon-refresh"),m=xn("icon-refresh-small"),v=xn("icon-refill"),y=xn("icon-submit"),w=xn("icon-stop"),b=kn("drag"),C=kn("tooltip");return Io(),qo("div",{ref:"chat",class:U(["ai-chat no-block-focus",{active:e.commonStore.isActive}]),"data-stick":e.chatStyle.stick,style:B(e.getComputedChatStyle()),onDragend:t[8]||(t[8]=(...t)=>e.onChangePosition&&e.onChangePosition(...t))},[Kt(Jo("div",ju,null,512),[[b,{params:{y:{increase:"top",decrease:"height"}},handleDrag:e.handleDrag}]]),Kt(Jo("div",zu,null,512),[[b,{params:{x:{increase:"width",decrease:"right"}},handleDrag:e.handleDrag}]]),Kt(Jo("div",Bu,null,512),[[b,{params:{y:{increase:"height"}},handleDrag:e.handleDrag}]]),Kt(Jo("div",Nu,null,512),[[b,{params:{x:{decrease:"width"}},handleDrag:e.handleDrag}]]),Kt(Jo("div",Fu,null,512),[[b,{params:{x:{decrease:"width"},y:{increase:"top",decrease:"height"}},handleDrag:e.handleDrag}]]),Kt(Jo("div",Vu,null,512),[[b,{params:{x:{increase:"width",decrease:"right"},y:{increase:"top",decrease:"height"}},handleDrag:e.handleDrag}]]),Kt(Jo("div",Iu,null,512),[[b,{params:{x:{increase:"width",decrease:"right"},y:{increase:"height"}},handleDrag:e.handleDrag}]]),Kt(Jo("div",Uu,null,512),[[b,{params:{x:{decrease:"width"},y:{increase:"height"}},handleDrag:e.handleDrag}]]),Jo("div",Wu,[Kt((Io(),qo("div",Zu,[ei(r),t[9]||(t[9]=oi()),t[10]||(t[10]=Jo("span",null,"Чат с ИИ",-1))])),[[b,{params:{x:{decrease:"right"},y:{increase:"top"}},handleDrag:e.handleDrag}]]),Jo("div",qu,[Kt((Io(),qo("button",{onClick:t[0]||(t[0]=t=>e.cleanChat())},[ei(l)])),[[C,"Очистить чат"]]),Kt((Io(),qo("button",{onClick:t[1]||(t[1]=t=>e.placeRight())},[ei(a)])),[[C,"Прикрепить справа"]]),Kt((Io(),qo("button",{onClick:t[2]||(t[2]=t=>e.commonStore.hide()),class:"ai-button-close"},[ei(c)])),[[C,"Закрыть"]])])]),Jo("div",Gu,[e.initialized?(Io(),qo(jo,{key:0},[(Io(!0),qo(jo,null,Ln(e.messages,((t,n)=>(Io(),qo("div",{class:U(["ai-message",t.status,t.role])},[Jo("div",Yu,["loading"==t.status&&""==t.text?(Io(),Go(u,{key:0})):si("",!0),"system"==t.role?(Io(),Go(d,{key:1})):si("",!0),(Io(!0),qo(jo,null,Ln(t.getTextByBlocks(),(e=>(Io(),qo(jo,null,["code"==e.type?(Io(),Go(p,{key:0,message:t,textBlock:e},null,8,["message","textBlock"])):si("",!0),"text"==e.type?(Io(),Go(h,{key:1,message:t,textBlock:e},null,8,["message","textBlock"])):si("",!0)],64)))),256))]),Jo("div",Ku,[Jo("div",Xu,[Kt((Io(),qo("button",{onClick:n=>e.copy(n,t)},[ei(f)],8,Qu)),[[C,"Копировать"]]),n+1==e.messages.length&&"loading"!=t.status&&"assistant"==t.role?Kt((Io(),qo("button",{key:0,onClick:o=>e.repeat(t,n)},[ei(g)],8,Ju)),[[C,"Повторить"]]):si("",!0)]),Jo("div",ed,["loading"==t.status&&""!=t.text?(Io(),Go(u,{key:0})):si("",!0)])])],2)))),256)),e.messages.length?si("",!0):(Io(),qo("div",td,"Спросите что-нибудь..."))],64)):(Io(),qo("div",nd,"Загрузка..."))],512),Jo("div",od,[Jo("form",{onSubmit:t[7]||(t[7]=rs(((...t)=>e.sendQuestion&&e.sendQuestion(...t)),["prevent"])),class:"ai-message-form"},[Kt(Jo("textarea",{"onUpdate:modelValue":t[3]||(t[3]=t=>e.question=t),placeholder:e.t("Ваш вопрос"),class:"ai-message-textarea",ref:"message-textarea"},null,8,id),[[os,e.question]]),null!==e.balance?(Io(),qo("div",sd,[Jo("span",rd,[t[12]||(t[12]=oi("Баланс: ")),Jo("span",{class:U({"ai-loading":e.balanceReloading})},[Jo("strong",null,Y(e.balance),1),t[11]||(t[11]=oi(" запросов"))],2)]),Kt((Io(),qo("button",{onClick:t[4]||(t[4]=t=>e.refreshBalance(t)),class:"ai-balance-refresh"},[ei(m)])),[[C,"Обновить"]]),e.balanceRefillUrl?Kt((Io(),qo("button",{key:0,onClick:t[5]||(t[5]=t=>e.refillBalance()),class:"ai-balance-refill"},[ei(v)])),[[C,"Пополнить"]]):si("",!0)])):si("",!0),"ok"==e.formState||"blocked"==e.formState?Kt((Io(),qo("button",{key:1,type:"submit",class:"ai-message-send",disabled:"blocked"==e.formState||""==e.question},[ei(y)],8,ld)),[[C,"Отправить (CTRL+Enter)",void 0,{left:!0}]]):si("",!0),"loading"==e.formState?(Io(),qo("button",{key:2,type:"button",class:"ai-message-stop",onClick:t[6]||(t[6]=(...t)=>e.stopGeneration&&e.stopGeneration(...t))},[ei(w)])):si("",!0)],32)])],46,Hu)}],["__scopeId","data-v-6b87abd2"]]),cd={__name:"App",setup:e=>(e,t)=>(Io(),qo(jo,null,[ei(ad),ei(ja)],64))};function ud(e){return e&&e.__esModule&&Object.prototype.hasOwnProperty.call(e,"default")?e.default:e}var dd,pd={exports:{}};const hd=ud(function(){if(dd)return pd.exports;dd=1;var e=document.implementation.createHTMLDocument("").createElement("div");function t(t){e.innerHTML=t;for(var n=e.querySelectorAll("*");n.length;)e.innerHTML=e.innerText,n=e.querySelectorAll("*");return e.innerText}function n(e){try{return e.toString().replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")}catch(fd){return""}}function o(t){e.innerHTML=t;for(var n=e.querySelectorAll("*"),o=n.length-1;o>=0;o--){var i=n[o],s=i.localName;if("script"!=s&&"noscript"!=s&&"noembed"!=s&&i.attributes instanceof NamedNodeMap){if(i.hasAttributes())for(var r=i.attributes,l=r.length-1;l>=0;l--){var a=r[l],c=a.localName,u=a.value.replace(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205f\u3000]/g,"").toLowerCase().trim();0==c.indexOf("on")?i.removeAttribute(c):("src"!=c&&"href"!=c||0!=u.indexOf("javascript:"))&&(-1!=["audio","image","img","source","video"].indexOf(s)||"src"!=c&&"data"!=c||0!=u.indexOf("data:"))||i.removeAttribute(c)}}else try{i.parentNode.removeChild(i)}catch(fd){i.outerHTML=""}}return e.innerHTML}return pd.exports={install:function(e,i){e.directive("html-remove",{inserted:function(e,n){e.innerHTML=t(n.value)},mounted:function(e,n){e.innerHTML=t(n.value)},update:function(e,n){n.value!==n.oldValue&&(e.innerHTML=t(n.value))},updated:function(e,n){n.value!==n.oldValue&&(e.innerHTML=t(n.value))}}),e.directive("html-escape",{inserted:function(e,t){e.innerHTML=n(t.value)},mounted:function(e,t){e.innerHTML=n(t.value)},update:function(e,t){t.value!==t.oldValue&&(e.innerHTML=n(t.value))},updated:function(e,t){t.value!==t.oldValue&&(e.innerHTML=n(t.value))}}),e.directive("html-safe",{inserted:function(e,t){e.innerHTML=o(t.value)},mounted:function(e,t){e.innerHTML=o(t.value)},update:function(e,t){t.value!==t.oldValue&&(e.innerHTML=o(t.value))},updated:function(e,t){t.value!==t.oldValue&&(e.innerHTML=o(t.value))}})}},pd.exports.removeHTML=t,pd.exports.escapeHTML=n,pd.exports.safeHTML=o,pd.exports}());document.addEventListener("DOMContentLoaded",(function(){document.body.insertAdjacentHTML("beforeend",'<div id="ai-assistant"></div>'),ds(cd).use(hd).use(ha,{autoHideOnMousedown:!0,themes:{tooltip:{triggers:["hover","touch"]}}}).mount("#ai-assistant")}));

/**
 * Plugin ReadyScript, активирует сканирование штрихкодов и QR-кодов через приложение ReadyScript
 * Плагин инициализируется автоматически на все элементы с атрибутом data-app-scan
 */
(function($){
    $.fn.appScan = function(method) {
        const defaults = {
                refreshInterval:2000,
                autoPressEnter: true,    //Нажимать Enter после ввода результатов сканирования в поле input
                autoPressEvent: 'keydown', //Событие для автоматического нажатия Enter (keydown или keypress)
                triggerEvent: false,     //Событие для генерации, после вставки. Например: 'click'. false - не генерировать событие
                triggerTarget: '',       //Селектор элемента для события. Например: '.apply-button',
                triggerTargetContext: '*' //Селектор родительского элемента, в котором находится triggerTarget. Например: '.apply-button-wrapper'
            },
            args = arguments;

        return this.each(function() {

            let $this = $(this),
                data = $this.data('appScanPlugin');

            const methods = {
                init: function (initoptions) {
                    if (data) return;
                    data = {};
                    $this.data('appScanPlugin', data);
                    data.opt = $.extend({}, defaults, initoptions, $this.data('appScanOptions'));

                    createButton();
                }
            };

            //private
            const
                /**
                 * Создает кнопку сканирования
                 */
                createButton = function() {
                    let button = $('<a>')
                        .attr('class', 'btn btn-default app-scan-button m-l-5 m-r-5')
                        .attr('title', lang.t('Сканировать в приложении ReadyScript'))
                        .append('<i class="zmdi zmdi-fullscreen"></i>');

                    button.click(startScan);
                    button.insertAfter($this);
                    button.parent().trigger('new-content');
                },
                /**
                 * Обрабатывает нажатие на кнопку сканирования
                 */
                startScan = function() {
                    if ($.rs.loading.inProgress) {
                        return;
                    }

                    const formats = $this.data('appScan');
                    const filter = $this.data('appScanFilter');

                    $.rs.openDialog({
                        url: global.scanUrl,
                        extraParams: {
                            formats: formats,
                            filter: filter
                        },
                        dialogOptions: {
                            width:400
                        },
                        afterOpen: function(dialog) {
                            initEvents(dialog);
                            dialog.on('click', '[data-resend-url]', (event) => reSend(event, dialog));
                        },
                        close: function(dialog) {
                            clearInterval(dialog.data('refreshInterval'));
                        }
                    });
                },
                /**
                 * Обновляет сведения о запросе на сканирование
                 */
                refresh = function(dialog, resend) {
                    let refreshBlock = dialog.find('[data-scan-root]');
                    $.ajaxQuery({
                        loadingProgress: resend === true,
                        url: refreshBlock.data('refreshUrl'),
                        data: {
                            status: refreshBlock.data('status'),
                            resend: (resend ? 1 : 0)
                        },
                        success: function(response) {
                            if (response.changed) {
                                if (response.status === 'success') {
                                    $this.val(response.result);
                                    dialog.dialog('close');

                                    if (data.opt.autoPressEnter) {
                                        const keyboardEvent = new KeyboardEvent(data.opt.autoPressEvent, {
                                            code: 'Enter',
                                            key: 'Enter',
                                            charCode: 13,
                                            keyCode: 13,
                                            view: window,
                                            bubbles: true
                                        });
                                        $this.get(0).dispatchEvent(keyboardEvent);
                                    }

                                    if (data.opt.triggerEvent) {
                                        const triggerElement = $this.parents('*').first()
                                            .find(data.opt.triggerTarget)
                                            .get(0);

                                        if (triggerElement) {
                                            triggerElement.dispatchEvent(
                                                new Event(data.opt.triggerEvent, {bubbles: true}));
                                        }
                                    }
                                } else {
                                    refreshBlock.replaceWith(response.html);
                                    initEvents(dialog);
                                }
                            }
                        }
                    });
                },

                /**
                 * Отправляет Push уведомление на мобильное устройство повторно
                 */
                reSend = function(event, dialog) {
                    clearInterval(dialog.data('refreshInterval'));

                    let button = $(event.currentTarget);
                        button.text(button.data('sendingText'))
                              .prop('disabled', true);

                    refresh(dialog, true);
                },

                /**
                 * Инициализирует автоматическое обновление данных
                 */
                initEvents = function(dialog) {
                    let refreshBlock = dialog.find('[data-scan-root]');

                    clearInterval(dialog.data('refreshInterval'));
                    if (refreshBlock.data('status') === 'waiting') {
                        dialog.data('refreshInterval', setInterval(() => refresh(dialog),
                            data.opt.refreshInterval));
                    }
                };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }
})(jQuery);

$.contentReady(function() {
    $('input[data-app-scan]').appScan();
});
