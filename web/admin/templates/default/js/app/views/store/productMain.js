/** @jsx React.DOM **/
;(function($, window){
    var App = window.App;

    App.Views.Product = App.Views.Product || {};

    App.Views.Product.Main = React.createClass({
        mixins: [Backbone.React.Component.mixin],
        handleInputSubmit: function(q) {
            this.getCollection().fetch({
                data: $.param({
                    q: q,
                    action: '_products',
                    limit: 25
                })
            });
        },
        componentDidMount: function() {
            this.getCollection().fetch({
                data: $.param({
                    action: '_products',
                    limit: 25
                })
            });
            /*
            $.get(this.props.source, function(result) {
                var lastGist = result[0];
                if (this.isMounted()) {
                    this.setState({
                        username: lastGist.owner.login,
                        lastGistUrl: lastGist.html_url
                    });
                }
            }.bind(this));
            */
        },
        render: function() {
            return (
                <div>
                    <App.Views.Product.SearchBox onInputSubmit={this.handleInputSubmit} />
                    <App.Views.Product.List data={this.props.collection} />
                </div>
            );
        }
    });

    React.render(<App.Views.Product.Main collection={new App.Collections.Products} />, document.getElementById('app'));

    window.App = App;
})(jQuery, window);