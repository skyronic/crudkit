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
            rows: [],
            currentPage: 0,
            loading: false,
            pageCount: 5,
            rowsPerPage: 10
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
    changePage: function (page) {

    },
    render: function () {
        return (
            <div>
                <table className="table">
                    <TableHead schema={this.state.schema} />
                    <TableBody schema={this.state.schema} rows={this.state.rows} />
                </table>
                <TablePagination onPageChange={this.changePage} currentPage={this.state.currentPage} loadingFlag={this.state.loading} pageCount={this.state.pageCount} />
            </div>
        )
    }
});

var TablePagination = React.createClass ({
    getInitialProps: function () {
        return {
            loadingFlag: false,
            pageCount: 1,
            currentPage: 0
        }
    },
    changePage: function (i, e) {
        console.log("Page is now ", i)
        
    },
    render: function () {
        var loadingClass = this.props.loadingFlag ? 'hidden' : '';
        var self = this;

        var generatePageNodes = function () {
            var items = [];
            for(var i = 1; i <= self.props.pageCount; i++) {
                var activeClass =
                items.push(<li><a href="#" onClick={self.changePage.bind(null, i)} >{i}</a></li>);
            }

            return items;
        };

        return (
            <div className="row">
                <div className="col-md-4">
                    <div className={loadingClass}>
                        Loading...
                    </div>
                </div>
                <div className="col-md-8">
                    <nav>
                        <ul className="pagination">
                            <li>
                                <a href="#">
                                    <span>&laquo;</span>
                                </a>
                            </li>
                            {generatePageNodes()}
                            <li>
                                <a href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        )
    }
});

React.render(<SummaryTable pageId={window.pageId} />, document.getElementById ("dataTable"));

