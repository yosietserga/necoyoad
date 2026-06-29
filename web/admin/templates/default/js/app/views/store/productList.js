/** @jsx React.DOM **/
;(function($, window){
    var App = window.App;

    App.Views.Product = App.Views.Product || {};

    App.Views.Product.List = React.createClass({
        render: function() {
            var listItems = [],
                pagination = [];

            console.log(this.props.data);
            if (this.props.data) {
                listItems = this.props.data.map(function(item) {
                    return (
                        <App.Views.Product.ListItems attributes={item.attributes} />
                    );
                });
            }

            return (
                <ul>
                    {listItems}
                </ul>
            );
        }
    });

    window.App = App;
})(jQuery, window);