# WowFramework
A lightweight, extendable, multilingual and powerful PHP MVC-L framework. Includes only general needs and optimized for speed.

##System Requirements
PHP 5.6+

Wow Framework is compatible with PHP 7.x

Tested on  both Windows and Linux based hosts.

##Routing
The routing mechanism finds the Classname, Class Method and Method parameters from url.
A url like /sales/latest-sales extends to
* Controller : SalesController
* Action: LatestSalesAction
* View Name: sales/latest-sales

The view folders and view file names are always lowercase. Because linux platforms are case sensitive in filesystem but windows not. Lowercasing all the files help us to fix case sensivity problems.
Also all the controller file names are capitalized. So a controller class file with a name SalesManagerController must be SalesManagerController.php

Another Sample:
A url like /sales-manager/get-sale/11 extends to
* Controller : SalesManagerController
* Action: GetSaleAction(11)
* View Name: sales-manager/get-sale

Another Sample:
A url like /salesManager/getSale/11 extends to
* Controller : SalesmanagerController
* Action: GetsaleAction(11)
* View Name: salesmanager/getsale

This is the default routing schema 
```
(@controller(@action(@id)))
```

If you want a route to answer only defined methods the pattern must be
```
GET|POST (@controller(@action(@id)))
```
The pharanteses "()" means that this parameter can pass null. If some parameters null then the default parameter used.
A sample for this:
Our routes are these
```
        "routes"   => array(
            "DefaultRoute" => array(
                "(/@controller(/@action(/@id)))",
                array(
                    "prefix"     => "",
                    "controller" => "Home",
                    "action"     => "Index"
                )
            )
        )
```
So the url /About extends to
* Controller : AboutController
* Action: IndexAction()
* View Name: about/index

You can add a prefix folder to your routes, and group your controllers in folders.

Routes are defined in /app/Config/routes.php file!

We suggest you to use lowercase letters in yoru uri. This is the best choice in WowFramework.

The route matching mechanism suports case-sensitive and case-insensitive matching. Default is case-insensitive. You can change it app/Config/config.php file. 

The case-insensitive route matching means that: if you define a route like /upgrade(/@conroller(/@method)) does not match for uri /Upgrade/version/latest because of Upgrade not equals upgrade!

Also you can you can use regex patterns. A sample for this (In this sample categoryid is optional):

```
        "routes"   => array(
            "CategoryRoute" => array(
                "/category/[\w-]+(/@categoryid)",
                array(
                    "prefix"     => "",
                    "controller" => "Products",
                    "action"     => "List"
                )
            )
        )
```

Also you can you can use regex patterns for named params. In this way param must match with pattern. You should add :{ YOUR REGEX PATTERN }

```
        "routes"   => array(
            "ProductDetailsRoute" => array(
                "/@slug:{[\w-]+}-product@id:{[0-9]+}",
                array(
                    "prefix"     => "",
                    "controller" => "Product",
                    "action"     => "Detail"
                )
            )
        )
```

##Controllers
Controllers are stored in app/Controllers folder under namespace App\Controllers

### Controller Classes
You must capitalize the first letters of your Controller's name and also file name, and add Controller to end of name.

For example a controller named Acccount:

    File name  : AccountController.php
    Class name : AccountController
    
Another example named SalesManager:

    File name  : SalesManagerController.php
    Class name : SalesManagerController
    
You must extend Wow's Controller class for all your controllers. Otherwise you can use BaseController's that extends the Wow's Controller class.

Wow's Controller class includes core functions onActionExecuting and onActionExecuted. These functions can be overwritten for your needs.

onActionExecuting function fires before executing the Action coming from Route.

onActionExecuted function executes after the action method executed and returned to vresult (view, json, redirect, notfound etc.)

These methods works perfect to manipulate parameters or responses.

### Controller Methods
Your controller class includes Actions to execute found by routing. You must add Action at the end to be executable to all your functions. If you create a function which is not an action result do not add Action. 

A sample Action method for Login method.
    
    File name    : AccountController.php
    Class name   : AccountController
    Class method : LoginAction

if you don't add Action to yout method name, it means that this method is not an action result.

You can add parameters to your action methods and Wow fills them automatically from route parameters and query string parameters. An example for this :

    Controller : ProductController
    Method     : EditAction($id)
    Uri        : /product/edit/1 or /product/edit?id=1
    Route      : (/@controller(/@action(/@id)))
    
Another example:

    Controller : ProductController
    Method     : ListAction($sort="id",$sortorder="desc")
    Uri        : /product/list or /product/list?sort=price&sortorder=desc
    Route      : (/@controller(/@action(/@id)))

If your method's parameter are not nullable and they dont come from route parameters or querystrinbg parameters route match fails! 

#### Result Types

All the actions must return a Response Object. There are some result types (Response's):

- ViewResult
- PartialViewResult
- JsonResult
- JsonPResult
- RedirectResult
- HttpNotFoundResult
- FileResult

These result types are declared as constants in Controller class.

A sample method that returns a ViewResult:

    function EditAction($id){
    
        if(!intval($id)>0){
            return $this->notFound();
        }
        
        $productDetails = $this->db->row("SELECT * FROM products WHERE id=",array("id"=>$id));
        
        if(empty($productDetails)){
            return $this->notFound();
        }
        
        return $this->view($productDetails);
    }

A JsonResult Sample:

    function SaveAction($id){
        $resp = array("status"=>"success","message"=>"Saved successfully");
        
        return $this->json($resp);
    }
    
    
A RedirectResult Sample:

    function SaveAction($id){
        
        return $this->redirectToUrl("/product/edit/". $id ."?saved=1");
    }
    
    
As you can see, the action methods can pass data. In view or partial view files these data can be used with name $model .

You can also pass data to views with named parameters.

An example for this:

    function EditAction($id){
    
        if(!intval($id)>0){
            return $this->notFound();
        }
        
        $productDetails = $this->db->row("SELECT * FROM products WHERE id=",array("id"=>$id));
        
        if(empty($productDetails)){
            return $this->notFound();
        }
        
        $firmDetails = $this->db->row("SELECT * FROM firms WHERE id=",array("id"=>$productDetails["firmid"]));
        
        $this->view->set("productDetails",$productDetails);
        $this->view->set("firmDetails",$firmDetails);
        
        return $this->view();
    }
    
#### Passing data to Views

Example:

    $this->view->set("title",$product["title"]);    

##Views
The view files are stored in app/Views folder. The view files are pure php files. Wow Framework's View Class has a name = value array which is used to pass data from Controller Method to View file. The get(name), set(name,value), has(name), clear(name) methods for name value pairs.

For example in a view file you can use:

    $this->set("title","Home");
   
Also yo can manipulate a defined:
    
    $titleFromController = $this->get("title");
    $title = $titleFromController." | My Site Name";
    $this->set("title",$title);
    
#### Layouts
A view file can be used as master view. These files are generally stored in app/Views/{theme_name}/layout

Layout files can include sections. A sample code for section;
    
    <html>
    <head>
    <title><?php echo $this->get("title"); ?></title>
    </head>
    <body>
    <?php $this->renderBody(); ?>
    <?php $this->section("section_scripts"); ?>
    <script src="/assets/path/to/script.js"></script>
    <?php $this->show(); ?>
    </body>
    </html>
    
The renderBody() method appends html wihch came from ActionResult's View file.
ActionResult's View file can manipulate the sections. We will tell it in Sections.

#### Sections

Preparing..
