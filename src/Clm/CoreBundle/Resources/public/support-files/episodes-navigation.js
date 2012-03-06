var episodes = [
{
title: "Episode 1: Christ's Light on the Spiritual Journey",
url: 'christs-light-radio-episode-1.html'
},
{
title: "Episode 2: Christ's Light on the End of All Suffering",
url: 'christs-light-radio-episode-2.html'
},
{
title: "Episode 3: Christ's Light on the Nature of Enlightenment",
url: 'christs-light-radio-episode-3.html'
},
{
title: "Enlightenment Meditation from Episode 3",
url: 'christs-light-radio-enlightenment-meditation-from-episode-3.html'
},
{
title: "Episode 4: Enliven Your Christian Walk with the Holy Spirit",
url: 'christs-light-radio-episode-4-enliven-your-christian-walk-with-the-holy-spirit.html'
},
{
title: "Episode 5: Christ's Light on Speaking with Your Angels",
url: 'christs-light-radio-episode-5-christs-light-on-speaking-with-your-angels.html'
},
{
title: "Spirit Guide and Angel Meditation from Episode 5",
url: 'christs-light-radio-spirit-guide-and-angel-meditation-from-episode-5.html'
}
];

function getFirstEpisode()
{
return episodes[0]
}

function getLastEpisode()
{
return episodes[episodes.length-1];
}

function getPreviousEpisode(current)
{
if (current > 1)
{
return episodes[current-2];
}
}

function getNextEpisode(current)
{
if (current <= episodes.length)
{
return episodes[current];
}
}

function formatEpisodes(current)
{
ul = document.createElement('ul');
ul.setAttribute('class', 'horrizontal-list');

if (current > 2)
{
ul.appendChild(formatEpisode(getFirstEpisode(), "\u00ab First"));
}

if (current > 1)
{
ul.appendChild(formatEpisode(getPreviousEpisode(current), "\u003c Previous"));
}

episodeItem = document.createElement('li');
episodeItem.appendChild(document.createTextNode(episodes[current-1]['title']));
ul.appendChild(episodeItem);

if (current < episodes.length)
{
ul.appendChild(formatEpisode(getNextEpisode(current), "Next \u003e"));
}

if (current < episodes.length-1)
{
ul.appendChild(formatEpisode(getLastEpisode(), "Last \u00bb"));
}

return ul;
}

function formatEpisode(episode, text)
{
episodeItem = document.createElement('li');
episodeItemLink = document.createElement('a');
episodeItemLink.setAttribute('href', episode['url']);
episodeItemLink.setAttribute('title', episode['title']);
episodeItemLink.appendChild(document.createTextNode(text));

episodeItem.appendChild(episodeItemLink);

return episodeItem;
}

onload=function(){
if (document.getElementsByClassName == undefined) {
	document.getElementsByClassName = function(className)
	{
		var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
		var allElements = document.getElementsByTagName("*");
		var results = [];

		var element;
		for (var i = 0; (element = allElements[i]) != null; i++) {
			var elementClass = element.className;
			if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
				results.push(element);
		}

		return results;
	}
}
}

function populateEpisodeLists(episode)
{
episode_list = document.getElementsByClassName('episode-navigation');
for (var i = 0; i < episode_list.length; i++)
{
episode_list[i].appendChild(formatEpisodes(episode));
}
}
