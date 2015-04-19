var ckUrl = {
    resetGetParams: function (params) {
        var url = new Url();
        url.query.clear();

        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                url.query[key] = params[key];
            }
        }

        return url.toString();
    },
    addGetParams: function (params) {
        var url = new Url();
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                url.query[key] = params[key];
            }
        }
        return url.toString();
    }
};

window.ckUrl = ckUrl;
var ckAjax = {
    makeRequest: function (url, params, done) {
        $.ajax(url, {
            method: "POST",
            data: params,
            success: function (data) {
                done(data);
            },
            error: function () {

            }
        })
    },
    page: {
        func: function (page_id, func_name, params, done) {
            ckAjax.makeRequest(ckUrl.resetGetParams({
                page: page_id,
                func: func_name,
                action: "page_function"
            }), params, done);
        }
    }
};


var SummaryTable = React.createClass({
    getInitialProps: function () {
        return {
            pageId: ""
        }
    },
    getInitialState: function () {
        return {
            schema: {},
            rows: {}
        }
    },
    componentWillMount: function () {
        var self = this;

        ckAjax.page.func(this.props.pageId, "get_summary_data", {}, function (data) {
            self.setState({
                schema: data.schema,
                rows: data.data
            });
        })
    },
    render: function () {
        return (
            <div>
                <table className="table">

                </table>
            </div>
        )
    }
});

React.render(<SummaryTable pageId={window.pageId} />, document.getElementById ("dataTable"));

