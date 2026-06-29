/** @jsx React.DOM **/
;(function($, window){
    var App = window.App;

    App.Views.Product = App.Views.Product || {};

    App.Views.Product.SearchBox = React.createClass({
        handleSubmit: function(e) {
            e.preventDefault();
            var q = this.refs.searchInput.getDOMNode().value;
            console.log(q);
            this.props.onInputSubmit(q);
        },
        render: function() {
            return (
                <div className="search-box-wrapper">
                    <form id="character-search=form" className="cf" onSubmit={this.handleSubmit}>
                        <input type="text" id="search" onChange={this.handleSubmit} ref="searchInput" name="search" placeholder="searcg..." />
                        <button className="button">Search</button>
                    </form>
                </div>
            );
        }
    });

    window.App = App;
})(jQuery, window);