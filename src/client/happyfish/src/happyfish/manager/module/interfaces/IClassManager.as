package happyfish.manager.module.interfaces 
{
	
	/**
	 * ...
	 * @author jj
	 */
	public interface IClassManager 
	{
		function getClass(className:String):Class;
		function hasClass(className:String):Boolean;
	}
	
}