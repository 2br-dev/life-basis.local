/**
 * Плагин, отвечающий за работу виджета Статистики запросов к ИИ в административной панели ReadyScript
 */
$.widget('rs.aiStatistic', {
    options:{
        placeholder: '.placeholder',
        chartsFilter: '.chart-filter',
        chartsCheckbox: '.chart-filter input',
        lastyearPlotOptions: {
            xaxis: {
                minTickSize: [1, "month"],
            }
        },
        lastmonthPlotOptions: {
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
                tickDecimals:0,
                min:0
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
            }
        }
    },
    _create: function() {
        this.chart = $(this.options.placeholder, this.element);

        this.element
            .on('change', this.options.chartsCheckbox, (e) => {
                //Если это последний чек-бокс, то не даем снять
                if ($(e.currentTarget).closest('ul').find(':checked').length === 0) {
                    e.currentTarget.checked = true;
                    return;
                }

                this.build();
            })
            .on('click', this.options.chartsFilter, (e) => {e.stopPropagation();});

        this.build();

        this.chart.on("plothover", (event, pos, item) => {
            this._plotHover(event, pos, item);
        });
    },

    build: function() {
        this.dataset = [],
        this.chartsList = $(this.options.chartsCheckbox + ':checked', this.element),
        this.sourceDataset = this.chart.data('inlineData').points;

        if (this.chartsList.length) {
            this.chartsList.each((k,v) => {
                let key = $(v).val();
                if (this.sourceDataset[key]) {
                    this.dataset.push(this.sourceDataset[key]);
                }
            });
        }

            let options = $.extend(true,
                this.options.plotOptions,
                this.options[this.chart.data('inlineData').range + 'PlotOptions']);

            $.plot(this.chart, this.dataset, options);
    },

    _plotHover: function(event, pos, item) {
        if (item) {
            if (this.previousPoint !== item.dataIndex) {
                this.previousPoint = item.dataIndex;

                let point = this.dataset[item.seriesIndex].data[item.dataIndex];
                let dateStr = this[('_' + this.chart.data('inlineData').range + 'Format')].call(this, point);

                let tooltipText = item.series.label + "<br>" + dateStr + ': ' + this.numberFormat(point[1]);
                this._showTooltip(item.pageX, item.pageY, tooltipText);
            }
        }
        else {
            $("#aiStatisticTooltip").remove();
            this.previousPoint = null;
        }
    },

    _showTooltip: function(x, y, contents) {
        $("#aiStatisticTooltip").remove();
        $('<div id="aiStatisticTooltip" class="chart-tooltip"/>').html(contents).css( {
            top: y + 10,
            left: x + 10
        }).appendTo("body").fadeIn(200);
    },

    _lastyearFormat: function(pointData) {
        let
            months = lang.t("Январь,Февраль,Март,Апрель,Май,Июнь,Июль,Август,Сентябрь,Октябрь,Ноябрь,Декабрь").split(','),
            pointDate = new Date(pointData[0]);

        return lang.t('%date', {date: months[pointDate.getMonth()] + ' ' + pointDate.getFullYear()});
    },

    _lastmonthFormat: function(pointData) {
        let
            months = lang.t("января,февраля,марта,апреля,мая,июня,июля,августа,сентября,октября,ноября,декабря").split(','),
            pointDate = new Date(pointData[0]);

        return pointDate.getDate()+' '+months[pointDate.getMonth()]+' '+pointDate.getFullYear();
    },

    numberFormat: function(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }
});