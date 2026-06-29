/** @jsx React.DOM **/
;(function($, window){
    var App = window.App;

    App.Views.Product = App.Views.Product || {};

    App.Views.Product.ListItems = React.createClass({
        render: function() {
            console.log(this.props.data);
            var model = this.props.attributes;
            return (
                <li className="large-12">
                    {model.pname} - hola
                </li>
            );
        }
    });

    window.App = App;
})(jQuery, window);