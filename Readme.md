# YEDI - Yet enough dependency injector

This is a small and easy dependency injection framework.

Features:

- DI Getter trait
- Fluent-speaking interface
- Containers are PSR-11 compatible...
- Delegate classes / interfaces to another and concrete class (see "Use of Alias Container")
- Instantiating dependencies with configured constructor arguments (see "Use of Resolution Container")


#### Use of Injector:

Whenever you want to inject a dependent class, you need the injector. 

First of all, this is not yet cumbersome:

    $di = new JayBeeR\YEDI\DependencyInjector;
    $object = $di->get(My\Own\Class::class);
        
Or you can use the DependencyInjectorGetter trait in your class:

    class MyOwnClass
    {
        use JayBeeR\YEDI\DependencyInjectorGetter;
        
        protected My\Own\Interface $stuff;
        
        public function __construct()
        {
            $this->stuff = $this->get(My\Own\OtherClass::class);
        }
    }
        
Every class without YEDI support can also be created internally in the pure DI type:

    class MyOwnClass
    {
        protected My\Own\Interface $stuff;
        
        public function __construct(My\Own\OtherClass $stuff)
        {
            $this->stuff = $stuff;
        }
    }
    
    $myClass = $di->get(MyOwnClass::class);

The given class type in the constructor argument $stuff will be fetched automatically and make a DI delegation before
 instantiating the requested class.

You can of course also use this class without YEDI:

    $dependentClass = new My\Own\OtherClass;
    $myClass = new MyOwnClass($dependentClass);

        
#### Use of Alias Container:

The alias container should be used for the mapping of classes to be overwritten. That should be (abstract) classes,
 interfaces or traits.

    $da = new JayBeeR\YEDI\DependencyAliasContainer();
    $da->delegate(ForeignClass::class)     ->to(MyExtendedClass::class);
    $da->delegate(ForeignInterface::class) ->to(MyClassWithThisInterface::class);
    $da->delegate(ForeignTrait::class)     ->to(MyClassWithThisTrait::class);

You can now use it as follows:
        
    $myExtendedClass = $di->get(ForeignClass::class);
    $myClassWithThisInterface = $di->get(ForeignInterface::class);
    $myClassWithThisTrait = $di->get(ForeignTrait::class);

From the outside the class, you now have control over the internally dependencies without have to extend this class.

But you have more support with YEDI. Continue reading.


#### Use of Resolution Container:

The resolution container should be used for the instantiating of a dependency class with concrete constructor
 arguments. 

    class MyClass {
        public function __construct(string $name, Foreign\HelperInterface $helper, int $limit, bool $hidden);
    }

    $dr = new JayBeeR\YEDI\DependencyResolutionContainer();
    $dr->for(ForeignClass::class)
        ->setArgument('limit')  ->asValue(123)
        ->setArgument('name')   ->asValue('value')
        ->setArgument('helper') ->asInjection(My\Own\Helper::class)
        ->setArgument('hidden') ->asValue(false)
    ;
    
    $foreignClass = $di->get(ForeignClass::class);
    
That would be the same as with the following implementation:
    
    $dependentHelper = new My\Own\Helper;
    $object = new ForeignClass(123, 'value', $dependentHelper, false);
       
But why you should use this?

Let's imagine that you have two classes that describe different implementations for an interface.

With the alias support from YEDI, however, you cannot delegate this interface to both classes.

This is the case:

    class MyClassA
    {
        public function __construct(ForeignInterface $interface);
    }

    class MyClassB
    {
        public function __construct(ForeignInterface $interface);
    }

That is the solution:

    $dr->for(MyClassA::class)
        ->setArgument(ForeignInterface::class)
        ->with(MyClassInterfaceA::class)
    ;

    $dr->for(MyClassB::class)
        ->setArgument(ForeignInterface::class)
        ->with(MyClassInterfaceB::class)
    ;
    
You can now instantiate with the simple class name:

    // constructor argument with MyClassInterfaceA
    $myClassA = $di->get(MyClassA::class); 
    
    // constructor argument with MyClassInterfaceB
    $myClassB = $di->get(MyClassB::class); 
    
 
## Wishlist

- Throws more Exceptions?
- Support of Singleton-like instantiation without interface (every class can be a Singleton)
- Refactoring and renaming things (clear interface)
- Tests for all exceptions
