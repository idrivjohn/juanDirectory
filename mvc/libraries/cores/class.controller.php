<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * class.controller.php V1.0
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Date   : 01 October 2014
 * Email  : jva.ipampanga@gmail.com
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
**/

namespace MVC;

(!defined('ROOTDIR'))?die('ILLEGAL ACCESS OF FILE'):'';

abstract class Controller extends Loader{
  function __construct(){
   // echo 'Extended: '.$this -> checkFile($page);
  }

}
?>