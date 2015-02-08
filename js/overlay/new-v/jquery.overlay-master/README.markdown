# jquery.overlay.js

Display a firebug-style box-model overlay on selected elements. Currently shows
overlay on hover. Useful for CSS demonstrations / tutorials.

## Usage

To enable an element to display its overlay on hover:

<pre><code>$('div').overlay();</code></pre>

To add a ‘static’ overlay:

<pre><code>$('div').add_overlay();</code></pre>

To display specific box-model properties:

<pre><code>$('div').add_overlay({ margin: true, content: true });</code></pre>
