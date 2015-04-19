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

var TableHead = React.createClass({
    getInitialProps: {
        schema: []
    },
    render: function () {
        var tableHeadRow = function (item) {
            return <th>{item.name}</th>
        };
        return (
            <thead>
            <tr>
            {this.props.schema.map(tableHeadRow)}
            </tr>
            </thead>
        )
    }
});

var TableBody = React.createClass({
    getInitialProps: {
        schema: [],
        rows: []
    },
    render: function () {
        var makeCell = function (item) {
            return (
                <td>
                {item}
                </td>
            )
        };
        var makeRow = function (row) {
            return (
                <tr>
                {row.map(makeCell)}
                </tr>
            )
        };
        return (
            <tbody>
            {this.props.rows.map(makeRow)}
            </tbody>
        )
    }
});

var SummaryTable = React.createClass({
    getInitialProps: function () {
        return {
            pageId: ""
        }
    },
    getInitialState: function () {
        return {
            schema: [],
            rows: []
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
                    <TableHead schema={this.state.schema} />
                    <TableBody schema={this.state.schema} rows={this.state.rows} />
                </table>
            </div>
        )
    }
});

React.render(<SummaryTable pageId={window.pageId} />, document.getElementById ("dataTable"));

