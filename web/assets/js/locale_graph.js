/* global currentLocale Dygraph */
/* exported graph */
var graph = new Dygraph(
    document.getElementById('graphdiv'),
    'https://l10n.mozilla-community.org/~pascalc/web_l10n_stats/logs/' + currentLocale + '.csv',
    {
        gridLineColor: 'lightgray',
        highlightCircleSize: 5,
        strokeWidth: 1,
        fillGraph: true,
        strokeBorderWidth: 1,
        gridLinePattern: [2,2],
        highlightSeriesOpts: {
            strokeWidth: 1,
            strokeBorderWidth: 1,
            highlightCircleSize: 3
        },
        axes: {
            x: {
                valueFormatter: function(val) {
                    // Format the date as YYYY-MM-DD
                    var date = new Date(val);
                    var d = date.getDate().toString();
                    var m = (date.getMonth() + 1).toString();
                    return date.getFullYear().toString() + '-' + (m <= 9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);
                }
            },
            y: {
                valueFormatter: function(val) {
                    var retval = val + ' missing string';
                    return val === 1 ? retval : retval + 's';
                }
            }
        }
    }
);
