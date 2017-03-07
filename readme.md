# ExpressionEngine Datagrab ChannelImages Fieldtype

Custom fieldtype for Datagrab add-on for ExpressionEngine.

### To Install:
1. Copy `datagrab_channel_images.php` to ajw_datagrab/fieldtypes in your EE addon directory.
2. Create import with channelimages field.
3. Run import.
4. In Channel Images, reprocess images.

This will create your images in your assigned directory, including grabbing them from their old or assigned URL, as well as process them according to your ChannelImages settings.

### Known Issues:
It is recommended to make a copy of your images prior to importing. In some set ups, the reprocess does not work.

Also, added a janky custom script for moving files into their assigned entry folders. ChannelImages doesn't always play nice with DataGrab.